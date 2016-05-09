<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
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
                $user = SimplerenewFactory::getContainer()->user->load($uid);

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
        /** @var Container $container */

        $account = $this->getState('account');
        if (!$account instanceof Account) {
            if ($user = $this->getUser()) {
                // Find first known gateway account
                $containers = SimplerenewFactory::getAllGatewayContainers();
                foreach ($containers as $container) {
                    try {
                        $account = $container->account->load($user);
                        $this->setState('account', $account);

                        // A bit of a hack - since Joomla doesn't store first/last names
                        $user->firstname = $account->firstname;
                        $user->lastname = $account->lastname;
                        break;

                    } catch (NotFound $e) {
                        // No account, no worries
                    }
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
                // Be sure to get billing from same gateway as account
                $container = SimplerenewFactory::getContainer($account->getGatewayName());
                try {
                    $billing = $container->billing->load($account);
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
        /** @var Container $container */

        $subscriptions = $this->getState('subscriptions', null);
        if ($subscriptions === null) {
            $subscriptions = array();

            if ($user = $this->getUser()) {
                $containers = SimplerenewFactory::getAllGatewayContainers();
                $status     = (int)$this->getState('status.subscription', null);

                // Gather subscriptions from all gateways
                $subscriptions = array();
                foreach ($containers as $container) {
                    try {
                        $account = $container->account->load($user);
                        $subscriptions = array_merge(
                            $subscriptions,
                            $container->subscription->getList($account, $status)
                        );

                    } catch (NotFound $e) {
                        // no account or subs, no problem
                    }
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
                // Gather invoices from all gateways that may have them
                $container = SimplerenewFactory::getContainer($account->getGatewayName());
                try {
                    $invoices = $container->invoice->getAccountList($account);

                } catch (NotFound $e) {
                    // No invoices, no problem
                }
            }
            $this->setState('invoices', $invoices);
        }

        return $invoices;
    }

    protected function populateState()
    {
        $userId = SimplerenewFactory::getUser()->get('id');
        $this->setState('user.id', $userId);
    }
}
