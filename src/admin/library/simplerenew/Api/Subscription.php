<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\SubscriptionInterface;

defined('_JEXEC') or die();

class Subscription extends AbstractApiBase
{
    const STATUS_ACTIVE   = 1;
    const STATUS_CANCELED = 2;
    const STATUS_EXPIRED  = 4;
    const STATUS_UNKNOWN  = 0;

    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $account_code = null;

    /**
     * @var string
     */
    public $plan = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var float
     */
    public $amount = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var int
     */
    public $quantity = null;

    /**
     * @var \DateTime
     */
    public $enrolled = null;

    /**
     * @var \DateTime
     */
    public $canceled = null;

    /**
     * @var \DateTime
     */
    public $expires = null;

    /**
     * @var \DateTime
     */
    public $period_start = null;

    /**
     * @var \DateTime
     */
    public $period_end = null;

    /**
     * @var \DateTime
     */
    public $trial_start = null;

    /**
     * @var \DateTime
     */
    public $trial_end = null;

    /**
     * @var string
     */
    public $pending_plan = null;

    /**
     * @var float
     */
    public $pending_amount = null;

    /**
     * @var SubscriptionInterface
     */
    protected $imp = null;

    /**
     * @param SubscriptionInterface $imp
     * @param array                 $config
     */
    public function __construct(SubscriptionInterface $imp, array $config = array())
    {
        $this->imp = $imp;
    }

    /**
     * Load a specific subscription by system id
     *
     * @param string $id
     *
     * @return Subscription
     * @throws Exception
     */
    public function load($id)
    {
        $this->clearProperties();

        $this->id = $id;
        $this->imp->load($this);

        return $this;
    }

    /**
     * Get list of subscriptions for the selected account
     *
     * @param Account $account
     * @param int     $bitMask Subscription status codes to retrieve
     *
     * @return array()
     */
    public function getList(Account $account, $bitMask = null)
    {
        return $this->imp->getList($this, $account, $bitMask);
    }

    /**
     * Load the most recent subscription on file
     * for the account
     *
     * @param Account $account
     *
     * @return Subscription
     */
    public function loadLast(Account $account)
    {
        $this->imp->loadLast($this, $account);
        return $this;
    }

    /**
     * Create a new subscription for the account and plan
     *
     * @param Account $account
     * @param Plan    $plan
     * @param Coupon  $coupon
     *
     * @return Subscription
     * @throws Exception
     */
    public function create(Account $account, Plan $plan, Coupon $coupon = null)
    {
        $this->clearProperties();

        $this->imp->create($this, $account, $plan, $coupon);
        $account->user->addGroups($plan->code);

        return $this;
    }

    /**
     * Cancel this subscription
     *
     * @return void
     * @throws Exception
     */
    public function cancel()
    {
        $this->imp->cancel($this);
    }

    /**
     * Turn autorenew on for this subscription
     *
     * @return void
     * @throws Exception
     */
    public function reactivate()
    {
        $this->imp->reactivate($this);
    }

    /**
     * Update subscription to a different plans
     *
     * @param Plan   $plan
     * @param Coupon $coupon
     *
     * @return void
     * @throws Exception
     */
    public function update(Plan $plan, Coupon $coupon = null)
    {
        $this->imp->update($this, $plan, $coupon);
    }

    /**
     * @param Account $account
     * @param string  $id
     *
     * @return Subscription
     * @throws NotFound
     */
    public function getValidSubscription(Account $account, $id)
    {
        $list = $this->getList($account);

        if (isset($list[$id])) {
            return $list[$id];
        }

        throw new NotFound('Subscription not found for account ' . $account->code);
    }
}
