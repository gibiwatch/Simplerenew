<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Exception\Duplicate;
use Simplerenew\Exception\NotFound;
use Simplerenew\Exception\NotSupported;
use Simplerenew\Gateway\SubscriptionInterface;
use Simplerenew\Plugin\Events;

defined('_JEXEC') or die();

class Subscription extends AbstractApiBase
{
    /**
     * Status codes are used as bitmasks
     */
    const STATUS_ACTIVE   = 1;
    const STATUS_CANCELED = 2;
    const STATUS_EXPIRED  = 4;
    const STATUS_UNKNOWN  = 0;

    /**
     * For use in termination
     */
    const REFUND_NONE    = 1;
    const REFUND_PARTIAL = 2;
    const REFUND_FULL    = 3;

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
    public $invoice_number = null;

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
     * @param Configuration         $config
     * @param SubscriptionInterface $imp
     * @param Events                $events
     */
    public function __construct(Configuration $config, SubscriptionInterface $imp, Events $events)
    {
        parent::__construct($config);

        $this->imp    = $imp;
        $this->events = $events;
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

        $this->events->trigger('simplerenewSubscriptionBeforeLoad', array($this));
        $this->imp->load($this);
        $this->events->trigger('simplerenewSubscriptionAfterLoad', array($this));

        return $this;
    }

    /**
     * Get list of subscriptions for the selected account
     *
     * @param Account $account
     * @param int     $bitMask Subscription status codes to retrieve
     *
     * @return Subscription[]
     */
    public function getList(Account $account, $bitMask = null)
    {
        $subscriptions = $this->imp->getList($this, $account, $bitMask);
        uasort($subscriptions, array($this, 'subscriptionSort'));

        return $subscriptions;
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

        $this->events->trigger('simplerenewSubscriptionBeforeCreate', array($this, $plan, $account, $coupon));

        if ($coupon && $coupon->isAvailable($plan)) {
            $this->imp->create($this, $account, $plan, $coupon);
        } else {
            $this->imp->create($this, $account, $plan);
        }
        $account->user->addGroups($plan->code);

        $this->events->trigger('simplerenewSubscriptionAfterCreate', array($this, $plan, $account, $coupon));

        return $this;
    }

    /**
     * Turn auto-renew off for this subscription
     *
     * @return Subscription
     * @throws Exception
     */
    public function cancel()
    {
        $this->events->trigger('simplerenewSubscriptionBeforeCancel', array($this));

        $this->imp->cancel($this);

        $this->events->trigger('simplerenewSubscriptionAfterCancel', array($this));

        return $this;
    }

    /**
     * Turn auto-renew on for this subscription
     *
     * @return Subscription
     * @throws Exception
     */
    public function reactivate()
    {
        $this->events->trigger('simplerenewSubscriptionBeforeReactivate', array($this));

        $this->imp->reactivate($this);

        $this->events->trigger('simplerenewSubscriptionAfterReactivate', array($this));

        return $this;
    }

    /**
     * Terminate this subscription
     *
     * @param int $refundType
     *
     * @return Subscription
     * @throws Exception
     */
    public function terminate($refundType = self::REFUND_PARTIAL)
    {
        $this->events->trigger('simplerenewSubscriptionBeforeTerminate', array($this));

        $this->imp->terminate($this, $refundType);

        $this->events->trigger('simplerenewSubscriptionAfterTerminate', array($this));

        return $this;
    }

    /**
     * Update subscription to a different plan
     *
     * @param Plan   $plan
     * @param Coupon $coupon
     * @param bool   $immediate
     *
     * @return Subscription
     * @throws Exception
     */
    public function update(Plan $plan, Coupon $coupon = null, $immediate = null)
    {
        $this->events->trigger('simplerenewSubscriptionBeforeUpdate', array($this, $plan));

        if ($immediate === null) {
            $immediate = $plan->isUpgradeFrom($this->plan);
        }
        $this->imp->update($this, $plan, $coupon, $immediate);

        $this->events->trigger('simplerenewSubscriptionAfterUpdate', array($this, $plan));

        return $this;
    }

    /**
     * Extend the subscription trial period by the interval amount.
     * Will throw error if Subscription is not in a trial period or
     * has already been extended once.
     *
     * @TODO: Check for previous extension here rather than in imps
     *
     * @param \DateInterval $interval
     *
     * @return $this
     * @throws NotSupported Subscription is not in trial
     * @throws Duplicate    Trial has already been extended once
     */
    public function extendTrial(\DateInterval $interval)
    {
        if (!$this->inTrial()) {
            throw new NotSupported('Subscription is not in trial');
        }
        $this->imp->extendTrial($this, $interval);

        $this->events->trigger('simplerenewSubscriptionAfterExtendTrial', array($this, $interval));

        return $this;
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

    /**
     * Whether multiple subscriptions are allowed
     *
     * @return bool
     */
    public function allowMultiple()
    {
        return $this->configuration->get('subscription.allowMultiple', false);
    }

    /**
     * Subscription in trial or not
     *
     * @return bool
     */
    public function inTrial()
    {
        if ($this->trial_start instanceof \DateTime && $this->trial_end instanceof \DateTime) {
            $now = new \DateTime();
            return ($now >= $this->trial_start && $now < $this->trial_end);
        }
        return false;
    }

    /**
     * Reverse sort subscriptions on current period ending date
     * with expired subscriptions on the bottom
     *
     * For use in uasort()
     *
     * @param Subscription $a
     * @param Subscription $b
     *
     * @return int
     */
    protected function subscriptionSort(Subscription $a, Subscription $b)
    {
        $aDate = $a->period_end;
        $bDate = $b->period_end;
        if (!$aDate instanceof \DateTime) {
            return $bDate instanceof \Datetime ? 1 : 0;

        } elseif (!$bDate instanceof \DateTime) {
            return 1;

        } else {
            $aTime = $aDate->getTimestamp();
            $bTime = $bDate->getTimestamp();

            $aExpired = $a->status == static::STATUS_EXPIRED;
            $bExpired = $b->status == static::STATUS_EXPIRED;

            if ($aExpired == $bExpired) {
                return $aTime < $bTime ? 1 : ($aTime > $bTime ? -1 : 0);
            } else {
                return $aExpired ? 1 : -1;
            }
        }
    }
}
