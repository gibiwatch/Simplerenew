<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerSubscription extends SimplerenewControllerBase
{
    public function display($cachable = false, $urlparams = array())
    {
        parent::display($cachable, $urlparams);
        echo '<h4>subscription controller under construction</h4>h4>';

        echo 'TASK: ' . $this->getTask();

        echo '<pre>';
        print_r($_REQUEST);
        echo '</pre>';
    }

    /**
     * New Subscriptions including expired
     */
    public function create()
    {
        $this->checktoken();

        SimplerenewHelper::saveFormData(
            'subscribe.create',
            array(
                'password',
                'password2',
                'billing.cc.number',
                'billing.cc.cvv'
            )
        );

        /** @var SimplerenewModelGateway $model */
        $model = SimplerenewModel::getInstance('Gateway');
        $app   = SimplerenewFactory::getApplication();

        // Create/Load the user
        try {
            $user = $model->saveUser();

        } catch (Exception $e) {
            return $this->callerReturn($e->getMessage(), 'error');
        }

        // Store the user id to the form data in case something goes wrong
        $formData = SimplerenewHelper::loadFormData('subscribe.create');

        $formData['userid'] = $user->id;
        SimplerenewHelper::saveFormData('subscribe.create', null, $formData);

        // Create the account
        try {
            $account = $model->saveAccount($user);
            $model->saveBilling($account);

        } catch (Exception $e) {
            return $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIBE_ACCOUNT', $e->getMessage()),
                'error'
            );
        }

        // All went well! Regardless of Joomla settings, log in the user if not already logged in
        $password = $app->input->getString('password');
        $user->login($password, true);

        try {
            $model->createSubscription($account, $app->input->getString('planCode'));
        } catch (Exception $e) {
            return $this->callerReturn(
                $e->getMessage(),
                'error'
            );
        }

        return $this->callerReturn('User/Account Create - need to send the user someplace');
    }

    /**
     * Change from one active subscription to another
     */
    public function change()
    {
        echo 'change subscriptions under construction';
    }

    /**
     * Change autorenew status
     */
    public function renewal()
    {
        echo 'cancel/autorenew under construction';
    }
}
