<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Subscription;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewSubscribe extends SimplerenewViewSite
{
    /**
     * @var array
     */
    protected $plans = array();

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @var Billing
     */
    protected $billing = null;

    /**
     * @var Subscription
     */
    protected $subscription = null;

    /**
     * @var bool
     */
    protected $newSubscription = null;

    public function display($tpl = null)
    {
        // Depending on user state, there may not be any plans to choose
        $this->plans = $this->get('Plans');
        if (!$this->plans) {
            $this->setLayout('noplans');
            parent::display($tpl);
            return;
        }

        try {
            if ($this->user = $this->get('User')) {
                $this->account      = $this->get('Account');
                $this->billing      = $this->get('billing');
                $this->subscription = $this->get('Subscription');
            }

        } catch (Exception $e) {
            // We don't care if they aren't logged in or don't have an account
        }

        // Set blank objects as needed
        $container          = SimplerenewFactory::getContainer();
        $this->user         = $this->user ? : $container->getUser();
        $this->account      = $this->account ? : $container->getAccount();
        $this->billing      = $this->billing ? : $container->getBilling();
        $this->subscription = $this->subscription ? : $container->getSubscription();

        $this->newSubscription = in_array(
            $this->subscription->status,
            array(
                Subscription::STATUS_EXPIRED,
                Subscription::STATUS_UNKNOWN
            )
        );

        // Fill in data from previous form attempt if any
        $dataSource = 'subscribe.' . ($this->newSubscription ? 'create' : 'change');
        if ($formData = SimplerenewHelper::loadFormData($dataSource)) {
            $this->user->setProperties($formData);

            $this->account->setProperties($formData);
            if (!empty($formData['billing'])) {
                $this->billing->setProperties($formData['billing']);
            }
        }

        if (!empty($formData['planCode'])) {
            $selectedPlan = $formData['planCode'];
        } elseif ($this->subscription->plan) {
            $selectedPlan = $this->subscription->plan;
        } else {
            $plan = current($this->plans);
            $selectedPlan = $plan->code;
        }

        foreach ($this->plans as $plan) {
            $plan->selected = ($selectedPlan == $plan->code);
        }

        parent::display($tpl);
    }
}
