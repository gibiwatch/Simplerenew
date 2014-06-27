<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

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

        echo '<h4>New subscriptions under construction</h4>';

        SimplerenewHelper::saveFormData('subscribe.create');

        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $user      = $container->getUser();
        $account   = $container->getAccount();

        if ($userId = $app->input->getInt('user_id')) {
            $user->load($userId);
        } else {
            $password = $app->input->getString('password');
            $password2 = $app->input->getString('password2');
            if (!$password) {
                return $this->callerReturn(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_EMPTY'), 'error');

            } elseif ($password !== $password2) {
                return $this->callerReturn(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_MISMATCH'), 'error');
            }

            $user->setProperties(
                array(
                    'firstname' => $app->input->getString('firstname'),
                    'lastname' => $app->input->getString('lastname'),
                    'username' => $app->input->getUsername('username'),
                    'email' => $app->input->getString('email'),
                    'password' => $app->input->getString('password')
                )
            );
            try {
                $user->create();
            } catch (Exception $e) {
                return $this->callerReturn($e->getMessage(), 'error');
            }
        }

        echo '<pre>';
        print_r($user->getProperties());
        echo '</pre>';

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
