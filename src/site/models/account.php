<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Container;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewModelAccount extends SimplerenewModelSite
{
    /**
     * @return User
     */
    public function getUser()
    {
        $user = $this->getState('user.user');
        if (!$user instanceof User) {
            $uid = (int)$this->getState('user.id');
            try {
                $user = $this->getContainer()
                    ->getUser()
                    ->load($uid);
            } catch (NotFound $e) {
                // most likely not logged in
            }

            $this->setState('user.user', $user);
        }
        return $user;
    }

    /**
     * @return null|Account
     */
    public function getAccount()
    {
        $account = $this->getState('account');
        if (!$account instanceof Account) {
            if ($user = $this->getUser()) {
                try {
                    $account = $this->getContainer()
                        ->getAccount()
                        ->load($user);
                    $this->setState('account', $account);
                } catch (NotFound $e) {
                    // No account, no worries
                }
            }
        }
        return $account;
    }

    /**
     * @return null|Billing
     */
    public function getBilling()
    {
        $billing = $this->getState('account.billing');
        if (!$billing instanceof Billing) {
            if ($account = $this->getAccount()) {
                try {
                    $billing = $this->getContainer()
                        ->getBilling()
                        ->load($account);
                    $this->setState('account.billing', $billing);
                } catch (NotFound $e) {
                    // No billing is okay
                }
            }
        }
        return $billing;
    }

    /**
     * @return null|Subscription
     *
     * @deprecated 0.2.0
     */
    public function getSubscription()
    {
        $subscription = $this->getState('subscription', null);
        if (!$subscription instanceof Subscription) {
            if ($account = $this->getAccount()) {
                try {
                    $subscription = $this->getContainer()
                        ->getSubscription()
                        ->loadLast($account);
                    $this->setState('subscription', $subscription);
                } catch (NotFound $e) {
                    // No Subscription is fine
                }
            }
        }

        return $subscription;
    }

    /**
     * Get array of subscriptions for the account. Use the
     * status.subscription bitmask to choose selected status codes
     *
     * @return array
     */
    public function getSubscriptions()
    {
        $subscriptions = $this->getState('subscriptions', null);
        if ($subscriptions === null) {
            $subscriptions = array();

            if ($account = $this->getAccount()) {
                try {
                    $status = (int)$this->getState('status.subscription', null);
                    $subscriptions = $this->getContainer()
                        ->getSubscription()
                        ->getList($account, $status);

                } catch (NotFound $e) {
                    // no subs, no problem
                }
            }
            $this->setState('subscriptions', $subscriptions);
        }

        return $subscriptions;
    }

    /**
     * @return null|Plan
     */
    public function getPlan()
    {
        $plan = $this->getState('subscription.plan', null);
        if (!$plan instanceof Plan) {
            if ($subscription = $this->getSubscription()) {
                $plan = $this->getContainer()
                    ->getPlan()
                    ->load($subscription->plan);
                $this->setState('subscription.plan', $plan);
            }
        }
        return $plan;
    }

    /**
     * @return null|Plan
     */
    public function getPending()
    {
        if ($subscription = $this->getSubscription()) {
            if ($subscription->pending_plan) {
                $pending = $this->getState('subscription.pending', null);
                if (!$pending instanceof Plan || $pending->plan_code != $subscription->pending_plan) {
                    $pending = $this->getContainer()
                        ->getPlan()
                        ->load($subscription->pending_plan);
                    $pending->amount = $subscription->pending_amount;
                    $this->setState('subscription.pending', $pending);
                }
            }
        }

        return empty($pending) ? null : $pending;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = $this->getState('container');
        if (!$container instanceof Container) {
            $container = SimplerenewFactory::getContainer();
            $this->setState('container', $container);
        }
        return $container;
    }

    protected function populateState()
    {
        $userId = SimplerenewFactory::getUser()->get('id');
        $this->setState('user.id', $userId);

        // We're only interested in current subscriptions
        $currentSubs = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
        $this->setState('status.subscription', $currentSubs);
    }
}
