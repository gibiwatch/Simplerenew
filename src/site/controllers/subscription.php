<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
     * Create new Subscriptions
     */
    public function create()
    {
        $this->checkToken();

        SimplerenewHelper::saveFormData(
            'subscribe',
            array(
                'password',
                'password2',
                'billing.cc.number',
                'billing.cc.cvv'
            )
        );

        $app   = SimplerenewFactory::getApplication();
        $model = $this->getGatewayModel();

        $planCodes = $app->input->get('planCodes', array(), 'array');
        $planCodes = array_filter(
            array_map(
                array('SimplerenewApplicationHelper', 'stringURLSafe'),
                $planCodes
            )
        );
        if (!$planCodes) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_NOPLAN_SELECTED'),
                'error'
            );
            return;
        }

        $events = SimplerenewFactory::getContainer()->events;
        $events->trigger('simplerenewSubscribeFormBeforeProcess', array($model, true));

        // Create/Load the user
        try {
            $user = $model->saveUser();

        } catch (Exception $e) {
            $this->callerReturn($e->getMessage(), 'error');
            return;
        }

        // Update/Create the account
        try {
            $account = $model->saveAccount($user);

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_ACCOUNT', $e->getMessage()),
                'error'
            );
            return;
        }

        // Update Billing
        try {
            $this->updateBilling($account);

        } catch (Exception $e) {
            $this->callerReturn($e->getMessage(), 'error');
            return;
        }

        // Add subscriptions
        /** @var Subscription $subscription */
        $couponCode = $app->input->getString('couponCode');
        try {
            $container = SimplerenewFactory::getContainer();

            foreach ($planCodes as $planCode) {
                if ($model->createSubscription($account, $planCode, $couponCode)) {
                    $plan = $container->getPlan()->load($planCode);
                    $app->enqueueMessage(JText::sprintf('COM_SIMPLERENEW_SUBSCRIPTION_PLAN_ADDED', $plan->name));
                }
            }

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_CREATE', $e->getMessage()),
                'error'
            );
            return;
        }

        /** @var SimplerenewModelSubscribe $model */
        $model = $this->getModel('Subscribe', 'SimplerenewModel');
        if ($itemid = $model->getParams()->get('newSubscriptionRedirect')) {
            $link = 'index.php?Itemid=' . (int)$itemid;
        } else {
            $link = SimplerenewRoute::get('account');
        }

        $this->setRedirect(JRoute::_($link));
    }

    /**
     * Change a subscription from one plan to another
     */
    public function change()
    {
        $this->checkToken();

        $app = SimplerenewFactory::getApplication();

        $id = $app->input->getString('id', null);
        if (!$id) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_NOID'),
                'error'
            );
            return;
        }

        $container = SimplerenewFactory::getContainer();
        $model     = $this->getGatewayModel();

        $container->events->trigger('simplerenewSubscribeFormBeforeProcess', array($model, false));

        // Only accept a single plan code
        $planCodes = $app->input->get('planCodes', array(), 'array');
        $planCodes = array_filter(
            array_map(
                array('SimplerenewApplicationHelper', 'stringURLSafe'),
                $planCodes
            )
        );
        $planCode  = array_shift($planCodes);

        try {
            $user    = $container->getUser()->load();
            $account = $container->getAccount()->load($user);

            // Update the billing info
            $model->saveBilling($account);

            $subscription = $container
                ->getSubscription()
                ->getValidSubscription($account, $id);

            if ($subscription->status == Subscription::STATUS_CANCELED) {
                $subscription->reactivate();
            }

            $newPlan = $container->getPlan()->load($planCode);
            $oldPlan = $container->getPlan()->load($subscription->plan);

            $couponCode = $app->input->getString('couponCode');
            $coupon     = $couponCode ? $container->getCoupon()->load($couponCode) : null;

            $subscription->update($newPlan, $coupon);

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
     * Update billing from the Billing Token in input stream
     *
     * @param Account $account
     *
     * @return void
     * @throws Exception
     */
    protected function updateBilling(Account $account)
    {
        $app   = SimplerenewFactory::getApplication();
        $model = $this->getGatewayModel();

        // Update billing
        try {
            $model->saveBilling($account);

        } catch (Exception $e) {
            throw new Exception(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_BILLING', $e->getMessage()),
                $e->getCode(),
                $e
            );
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
                JText::_('COM_SIMPLERENEW_WARN_SUBSCRIPTION_USER_LOGIN_FAILED'),
                'notice'
            );
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
