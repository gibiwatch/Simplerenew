<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewModelAccount extends SimplerenewModelSite
{
    public function getUser()
    {
        $user = $this->getState('user.user');
        if (!$user instanceof Simplerenew\User\User) {
            $uid = (int)$this->getState('user.id');
            $user = $this->getContainer()
                ->getUser()
                ->load($uid);

            $this->setState('user.user', $user);
        }
        return $user;
    }

    public function getAccount()
    {
        $account = $this->getState('account');
        if (!$account instanceof Simplerenew\Api\Account) {
            $user = $this->getUser();
            $account = $this->getContainer()
                ->getAccount()
                ->load($user);
            $this->setState('account', $account);
        }
        return $account;
    }
    public function getBilling()
    {
        $billing = $this->getState('account.billing');
        if (!$billing instanceof Simplerenew\Api\Billing) {
            $account = $this->getAccount();
            $billing = $this->getContainer()
                ->getBilling()
                ->load($account);
            $this->setState('account.billing', $billing);
        }
        return $billing;
    }

    public function getSubscription()
    {
        $subscription = $this->getState('subscription', null);
        if (!$subscription instanceof Simplerenew\Api\Subscription) {
            $account = $this->getAccount();
            $subscription = $this->getContainer()
                ->getSubscription()
                ->loadActive($account);

            $plan = $this->getContainer()
                ->getPlan()
                ->load($subscription->plan);

            $subscription->plan = $plan;
            $this->setState('subscription', $subscription);
        }

        return $subscription;
    }

    protected function getContainer()
    {
        $container = $this->getState('container');
        if (!$container instanceof Simplerenew\Container) {
            $container = SimplerenewFactory::getContainer();
            $this->setState('container', $container);
        }
        return $container;
    }

    protected function populateState()
    {
        $userId = SimplerenewFactory::getUser()->get('id');
        $this->setState('user.id', $userId);
    }
}
