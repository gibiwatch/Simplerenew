<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiSubscription extends RecurlyApibase
{
    protected $classname = 'Recurly_Subscription';

    /**
     * @var array Ordered list of plan types to determine upgrade/downgrade
     */
    protected $typeOrder = array('personal', 'pro');

    /**
     * Load subscription for the selected user
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function loadFromUser($id = null)
    {
        if ($id instanceof JUser) {
            $user = $id;
        } else {
            $user = JFactory::getUser($id);
        }

        if ($user->id > 0) {
            $accountCode = RecurlyApiAccount::getAccountCode($user->id);
            $subscriptions = iterator_to_array(Recurly_SubscriptionList::getForAccount($accountCode));
            $this->recurly = array_shift($subscriptions);
        }
        return $this;
    }

    public function isValid()
    {
        if (parent::isValid()) {
            return ($this->recurly->uuid != '');
        }
        return false;
    }

    /**
     * Create new subscription in Recurly
     *
     * @param mixed  $account
     * @param mixed  $plan
     * @param string $couponCode
     * @param bool   $groupDiscount
     *
     * @return RecurlyApiSubscription
     * @throws Exception
     */
    public static function create($account, $plan, $couponCode, $groupDiscount = false)
    {
        if ($account instanceof Recurly_Account || is_string($account)) {
            $account = new RecurlyApiAccount($account);
        }
        if ($plan instanceof Recurly_Plan || !is_object($plan)) {
            $plan = new RecurlyApiPlan($plan);
        }

        $subscription = new Recurly_Subscription();

        $subscription->account   = $account->recurly;
        $subscription->quantity  = 1;
        $subscription->currency  = 'USD';
        $subscription->plan_code = $plan->plan_code;

        if ($couponCode != '') {
            $subscription->coupon_code = $couponCode;
        }

        if ($groupDiscount) {
            $params = JComponentHelper::getParams('com_recurly');
            $price  = $plan->unit_amount_in_cents['USD']->amount_in_cents;

            $discount = $params->get('group_discount');
            $discount = $discount < 0 ? abs($discount) * 100 : $price * ($discount / 100);

            $subscription->unit_amount_in_cents = $price - $discount;
        }

        $subscription->create();
        return new RecurlyApiSubscription($subscription);
    }

    /**
     * See if user is authorised to edit/update this subscription
     * If $userId is not passed, currently logged in user will be checked
     *
     * @param int $userId
     *
     * @return bool
     */
    public function authorise($userId = null)
    {
        $group = new RecurlyApiGroup($userId);
        if ($group->user->id > 0 && $this->recurly instanceof $this->classname) {
            $account = $this->account->get();
            if ($account instanceof Recurly_Account) {
                $ownerId = array_pop(explode('_', $account->account_code));
                if ($group->authorise($ownerId)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getPrice()
    {
        return $this->unit_amount_in_cents / 100;
    }
}
