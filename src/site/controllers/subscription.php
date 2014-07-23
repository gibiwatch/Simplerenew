<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

class SimplerenewControllerSubscription extends SimplerenewControllerBase
{
    /**
     * @var SimplerenewModelGateway
     */
    protected $gatewayModel = null;

    public function display($cachable = false, $urlparams = array())
    {
        $app = SimplerenewFactory::getApplication();
        $app->enqueueMessage(
            JText::sprintf('COM_SIMPLERENEW_ERROR_UNKNOWN_TASK', $this->getTask()),
            'error'
        );
    }

    /**
     * New Subscriptions including expired
     */
    public function create()
    {
        $this->checkToken();

        SimplerenewHelper::saveFormData(
            'subscribe.create',
            array(
                'password',
                'password2',
                'billing.cc.number',
                'billing.cc.cvv'
            )
        );

        $app      = SimplerenewFactory::getApplication();
        $planCode = $app->input->getString('planCode');
        if (!$planCode) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_NOPLAN_SELECTED'),
                'error'
            );
            return;
        }

        $model = $this->getGatewayModel();

        // Create/Load the user
        try {
            $user = $model->saveUser();

        } catch (Exception $e) {
            $this->callerReturn($e->getMessage(), 'error');
            return;
        }

        // Create the account
        try {
            $account = $model->saveAccount($user);

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIBE_ACCOUNT', $e->getMessage()),
                'error'
            );
            return;
        }

        $method = $app->input->getCmd('payment_method');
        switch ($method) {
            case 'pp':
                $this->callerReturn(
                    'Payment via PayPal is not yet implemented',
                    'error'
                );
                return;

            case 'cc':
                $this->subscribeByCreditCard($account);
                break;

            default:
                $this->callerReturn(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_UNKNOWN_PAYMENT_METHOD', $method),
                    'error'
                );
                return;
                break;
        }

        $link = SimplerenewRoute::get('account');
        $this->setRedirect(
            JRoute::_($link),
            JText::_('COM_SIMPLERENEW_SUBSCRIPTION_SUCCESS')
        );
    }

    /**
     * Change from one active subscription to another
     */
    public function change()
    {
        $this->checkToken();

        $app = SimplerenewFactory::getApplication();
        $id  = $app->input->getString('id');

        if (!$id) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_NOID'),
                'error'
            );
            return;
        }

        $container = SimplerenewFactory::getContainer();
        try {
            $user    = $container->getUser()->load();
            $account = $container->getAccount()->load($user);

            // Update the billing info
            $this->getGatewayModel()->saveBilling($account);

            $subscription = $container
                ->getSubscription()
                ->getValidSubscription($account, $id);

            $planCode = $app->input->getString('planCode');

            if ($subscription->status == Subscription::STATUS_CANCELED) {
                $subscription->reactivate();
            }

            $oldPlan = $container->getPlan()->load($subscription->plan);
            $newPlan = $container->getPlan()->load($planCode);
            $subscription->update($newPlan);

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_CHANGE', $e->getMessage()),
                'error'
            );
            return;
        }

        $link = SimplerenewRoute::get('account');
        $this->setRedirect(
            JRoute::_($link),
            JText::sprintf(
                'COM_SIMPLERENEW_SUBSCRIPTION_CHANGE_SUCCESS',
                $oldPlan->name,
                $newPlan->name,
                $subscription->period_end->format('F, j, Y')
            )
        );
    }

    /**
     * Subscribe a new member using CC info in input stream
     *
     * @param Account $account
     */
    protected function subscribeByCreditCard(Account $account)
    {
        $app   = SimplerenewFactory::getApplication();
        $model = $this->getGatewayModel();

        // Update billing
        try {
            $model->saveBilling($account);
        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIBE_BILLING', $e->getMessage()),
                'error'
            );
            return;
        }

        // All went well! Valid billing information confirms the user so login them in
        try {
            $currentUser = SimplerenewFactory::getContainer()->getUser();
            try {
                $currentUser->load();

            } catch (Exception $e) {
                // No one logged in
            }

            // Logout current user if there is one
            if ($currentUser->id > 0 && $currentUser->id != $account->user->id) {
                $currentUser->logout();
            }

            //Regardless of Joomla settings, log in the user if not already logged in
            $password = $app->input->getString('password');
            $account->user->login($password, true);

            $currentUser->load();

        } catch (Exception $e) {
            // Not a big deal but leave a message
            $app->enqueueMessage(
                JText::_('COM_SIMPLERENEW_WARN_SUBSCRIBE_USER_LOGIN_FAILED'),
                'notice'
            );
        }

        // Create the subscription
        try {
            $model->createSubscription($account, $app->input->getString('planCode'));

        } catch (Exception $e) {
            $this->callerReturn(
                $e->getMessage(),
                'error'
            );
            return;
        }
    }

    /**
     * @return SimplerenewModelGateway
     */
    protected function getGatewayModel()
    {
        if ($this->gatewayModel === null) {
            $this->gatewayModel = SimplerenewModel::getInstance('Gateway');
        }
        return $this->gatewayModel;
    }
}
