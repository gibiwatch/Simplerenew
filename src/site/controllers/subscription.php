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

        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $user      = $container->getUser();
        $account   = $container->getAccount();

        $app->enqueueMessage('New subscriptions under construction', 'notice');

        // Create/Load the user
        try {
            if ($userId = $app->input->getInt('userid')) {
                $user->load($userId);
            } else {
                $password  = $app->input->getString('password');
                $password2 = $app->input->getString('password2');
                if (!$password) {
                    return $this->callerReturn(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_EMPTY'), 'error');

                } elseif ($password !== $password2) {
                    return $this->callerReturn(JText::_('COM_SIMPLERENEW_ERROR_PASSWORD_MISMATCH'), 'error');
                }

                $user->setProperties(
                    array(
                        'firstname' => $app->input->getString('firstname'),
                        'lastname'  => $app->input->getString('lastname'),
                        'username'  => $app->input->getUsername('username'),
                        'email'     => $app->input->getString('email'),
                        'password'  => $app->input->getString('password')
                    )
                );
                $user->create();

                // Store the user id to the form data in case something goes wrong
                $formData = SimplerenewHelper::loadFormData('subscribe.create');

                $formData['userid'] = $user->id;
                SimplerenewHelper::saveFormData('subscribe.create', null, $formData);
            }

        } catch (NotFound $e) {
            return $this->callerReturn($e->getMessage(), 'error');

        } catch (Exception $e) {
            return $this->callerReturn($e->getMessage(), 'error');
        }

        // Create the account
        try {
            try {
                $account->load($user);
            } catch (NotFound $e) {
                // Create a new account
            }

            $account->setProperties(
                array(
                    'firstname' => $app->input->getString('firstname'),
                    'lastname'  => $app->input->getString('lastname'),
                    'username'  => $app->input->getString('username'),
                    'email'     => $app->input->getString('email')
                )
            );
            $account->save();

        } catch (Exception $e) {
            $this->callerReturn(JText::sprintf('COM_SIMPLERENEW_ERROR_SUBSCRIBE_ACCOUNT', $e->getMessage()), 'error');
        }

        return $this->callerReturn('User/Account Create - more to come');
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
