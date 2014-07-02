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
use Simplerenew\Exception\NotFound;
use Simplerenew\Primitive\CreditCard;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewModelGateway extends SimplerenewModelSite
{
    /**
     * Create/update user
     *
     * @param array $data
     *
     * @return User
     * @throws Exception
     */
    public function saveUser(array $data = null)
    {
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $user      = $container->getUser();

        if ($data === null) {
            $data = array(
                'id'        => $app->input->getInt('userid'),
                'firstname' => $app->input->getString('firstname'),
                'lastname'  => $app->input->getString('lastname'),
                'username'  => $app->input->getUsername('username'),
                'email'     => $app->input->getString('email'),
                'password'  => $app->input->getString('password'),
                'password2' => $app->input->getString('password2')
            );
        }

        if (empty($data['id'])) {
            // Create a new user
            if (empty($data['password'])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_EMPTY'));

            } elseif (empty($data['password2']) || ($data['password'] !== $data['password2'])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_MISMATCH'));
            }

            $user->setProperties($data)->create();
        } else {
            // Specific user subscription request
            $currentUser = $container->getUser()->load();
            if ($currentUser->id && $currentUser->id != $data['id']) {
                if (!$user->validate($data['password'])) {
                    throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_INCORRECT'));
                }
            }
            // The requested user is logged in or password valid
            $user->setProperties($data)->update();
        }
        return $user;
    }

    /**
     * Create/update gateway account
     *
     * @param User  $user
     * @param array $data
     *
     * @return Account
     * @throws Exception
     */
    public function saveAccount(User $user, array $data = null)
    {
        $container = SimplerenewFactory::getContainer();
        $account   = $container->getAccount();

        try {
            $account->load($user);
        } catch (NotFound $e) {
            // Create a new account
        }

        if ($data === null) {
            $app  = SimplerenewFactory::getApplication();
            $data = array(
                'firstname' => $app->input->getString('firstname'),
                'lastname'  => $app->input->getString('lastname'),
                'username'  => $app->input->getString('username'),
                'email'     => $app->input->getString('email'),
                'company'   => $app->input->getString('company'),
                'address1'  => $app->input->getString('address1'),
                'address2'  => $app->input->getString('address2'),
                'city'      => $app->input->getString('city'),
                'region'    => $app->input->getString('region'),
                'country'   => $app->input->getString('country'),
                'postal'    => $app->input->getString('postal')
            );
        }

        $account->address->setProperties($data);
        $account->setProperties($data);
        $account->save();

        return $account;
    }

    /**
     * Update billing info for an account
     *
     * @param Account $account
     * @param array   $data
     *
     * @return Billing
     */
    public function saveBilling(Account $account, array $data = null)
    {
        $app = SimplerenewFactory::getApplication();
        if ($data === null) {
            $billingData = new JInput($app->input->get('billing', null, 'array'));
            $ccData      = new JInput($billingData->get('cc', null, 'array'));
            $data        = array(
                'firstname' => $billingData->getString('firstname'),
                'lastname'  => $billingData->getString('lastname'),
                'phone'     => $billingData->getString('phone'),
                'email'     => $billingData->getString('email'),
                'address1'  => $billingData->getString('address1'),
                'address2'  => $billingData->getString('address2'),
                'city'      => $billingData->getString('city'),
                'region'    => $billingData->getString('region'),
                'country'   => $billingData->getString('country'),
                'postal'    => $billingData->getString('postal'),
                'cc'        => array(
                    'number' => $ccData->getString('number'),
                    'cvv'    => $ccData->getString('cvv'),
                    'year'   => $ccData->getInt('year'),
                    'month'  => $ccData->getInt('month')
                )
            );
        }

        $container = SimplerenewFactory::getContainer();
        $billing   = $container->getBilling();
        $billing->load($account);

        if (array_filter($data['cc'])) {
            if (!$billing->payment instanceof CreditCard) {
                $billing->setPayment();
            }
            $billing->payment->setProperties($data['cc']);
        }

        $billing->address->setProperties($data);
        $billing->setProperties($data);
        $billing->save();

        return $billing;
    }

    /**
     * @param Account $account
     * @param Plan $plan
     *
     * @return Subscription
     */
    public function createSubscription(Account $account, Plan $plan)
    {
        $container    = SimplerenewFactory::getContainer();
        $subscription = $container->getSubscription();
        $subscription->create($account, $plan);

        return $subscription;
    }
}
