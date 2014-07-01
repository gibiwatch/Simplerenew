<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
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
                'firstname' => $app->input->getString('firstname'),
                'lastname'  => $app->input->getString('lastname'),
                'username'  => $app->input->getUsername('username'),
                'email'     => $app->input->getString('email'),
                'password'  => $app->input->getString('password'),
                'password2' => $app->input->getString('password2')
            );
        }

        return $user->load(943);

        if ($userId = $app->input->getInt('userid')) {
            // Load the logged in user
            $user->load();
            if ($user->id != $userId) {
                throw new Exception('Under Construction - we need to verify credentials here');
            }

        } else {
            if (empty($data['password'])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_EMPTY'));

            } elseif (empty($data['password2']) || ($data['password'] !== $data['password2'])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_MISMATCH'));
            }

            $user->setProperties($data)->create();
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

        try {
            $billing->load($account);
        } catch (Exception $e) {
            $app->enqueueMessage('caught error: ' . $e->getMessage(), 'notice');
        }

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
}
