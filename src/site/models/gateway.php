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
        $data      = new JRegistry($data ? : $this->getState()->getProperties());
        $user      = $container->getUser();

        if (!$data->get('userId')) {
            // Check for existing user
            try {
                $user->loadByUsername($data->get('username'));
                $data->set('userId', $user->id);

            } catch (NotFound $e) {
                // User doesn't exist is okay
            }
        }

        $password  = $data->get('password');
        $password2 = $data->get('password2');
        if ($data->get('userId') <= 0) {
            // Create a new user
            if (!$password) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_EMPTY'));

            } elseif (!$password2 || ($password !== $password2)) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_MISMATCH'));
            }
            $user->setProperties($data)->create();

        } else {
            // User exists, Verify existing credentials
            if (!$password || ($password !== $password2)) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_VERIFY_REQUIRED'));
            }
            if (!$user->validate($password)) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_INCORRECT'));
            }
            if (!$user->enabled && ($user->email != $data->get('email'))) {
                // For users not yet confirmed, we want to match the email address as well
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_EMAIL_VERIFY_REQUIRED'));
            }

            // We've verified we can do this
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

        $data = new JRegistry($data ? : $this->getState()->getProperties());
        $data = $data->toObject();

        $account->setProperties($data);
        $account->address->setProperties($data);
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

        $data = new JRegistry($data ? : $this->getState()->getProperties());
        $data = $data->toArray();

        $container = SimplerenewFactory::getContainer();
        $billing   = $container->getBilling();
        $billing->load($account);

        if (array_filter($data['cc'])) {
            if (!$billing->payment instanceof CreditCard) {
                $billing->setPayment();
            }
            $billing->payment->setProperties($data['cc']);
        }

        $billing->setProperties($data);
        $billing->address->setProperties($data);
        $billing->save();

        return $billing;
    }

    /**
     * @param Account $account
     * @param mixed   $plan
     *
     * @return Subscription
     * @throws Exception
     */
    public function createSubscription(Account $account, $plan)
    {
        $container    = SimplerenewFactory::getContainer();
        $subscription = $container->getSubscription();

        if (is_string($plan)) {
            $plan = $container->getPlan()->load($plan);
        }

        if (!$plan instanceof Simplerenew\Api\Plan) {
            throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_INVALID_ARGUMENT'));
        }
        $subscription->create($account, $plan);

        return $subscription;
    }

    protected function populateState()
    {
        $app = SimplerenewFactory::getApplication();

        $billingData = new JInput($app->input->get('billing', null, 'array'));
        $ccData      = new JInput($billingData->get('cc', null, 'array'));

        $data = array(
            'userId'    => $app->input->getInt('userId'),
            'firstname' => $app->input->getString('firstname'),
            'lastname'  => $app->input->getString('lastname'),
            'username'  => $app->input->getUsername('username'),
            'email'     => $app->input->getString('email'),
            'password'  => $app->input->getString('password'),
            'password2' => $app->input->getString('password2'),
            'billing'   => array(
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
            )
        );
        $this->state->setProperties($data);
    }
}
