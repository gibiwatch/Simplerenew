<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
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
     * Get all invoices for the account
     *
     * @return array
     */
    public function getInvoices()
    {
        $invoices = $this->getState('invoices', null);
        if ($invoices === null) {
            $invoices = array();

            if ($account = $this->getAccount()) {
                try {
                    $invoices = $this->getContainer()
                        ->getInvoice()
                        ->getAccountList($account);

                } catch (NotFound $e) {
                    // No invoices, no problem
                }
            }
            $this->setState('invoices', $invoices);
        }

        return $invoices;
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
    }
}
