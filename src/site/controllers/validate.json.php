<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerValidate extends SimplerenewControllerJson
{
    /**
     * Check for the availability of a proposed new username. For logged in users,
     * assume that this is a request to change their own username. Blank username
     * will validate as unavailable.
     *
     * @return void
     * @throws Exception
     */
    public function username()
    {
        $this->checkToken();

        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $currentUser = $container->getUser();
        $user        = $container->getUser();

        $id       = -1;
        $username = $app->input->getUsername('username');
        if ($username) {
            try {
                $user->loadByUsername($username);
                $currentUser->load();

            } catch (Exception $e) {

            }
        }

        $message = true;
        if (!empty($user->id)) {
            if ($currentUser->id) {
                // Logged in users can only access their own account
                if ($currentUser->id != $user->id) {
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_NOAUTH');
                }

            } else {
                // Guest users can enter password and email to authenticate
                $password = $app->input->getString('password');
                $email    = $app->input->getString('email');

                if (is_null($password) || is_null($email)) {
                    // Password or email is not being sent
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_REMOTE');

                } elseif (!$password && !$email) {
                    // Password/email sent but both empty
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_VERIFY');

                } elseif (!$password || !$user->validate($password)) {
                    // Need a password for verification
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_PASSWORD');

                } elseif (!$email || $user->email != $email) {
                    // Need an email for verification
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_EMAIL');
                }
            }
        }
        echo json_encode($message);
    }

    /**
     * Check for existence of proposed new email address. For logged in users,
     * assume that this is an attempt to change their own email address. Blank
     * email address will validate as unavailable.
     *
     * @return void
     * @throws Exception
     */
    public function email()
    {
        $this->checkToken();

        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $currentUser = $container->getUser();
        $user        = $container->getUser();

        $message = true;
        $email   = $app->input->getUsername('email');
        if ($email) {
            try {
                $user->loadByEmail($email);
                $currentUser->load();

            } catch (Exception $e) {

            }

            $targetUser = $container->getUser();
            if ($username = $app->input->getUsername('username')) {
                try {
                    $targetUser->loadByUsername($username);
                } catch (Exception $e) {
                    // Nonexistent is okay
                }
            }

            if (empty($user->id) && !empty($targetUser->id)) {
                // email not on file, but username does exist
                $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_INVALID');

            } elseif (!empty($user->id)) {
                if ($currentUser->id) {
                    if ($currentUser->id != $user->id) {
                        // Logged in user trying to change their email address
                        $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REMOTE');
                    }

                } else {
                    // Not logged in. Try to verify username match
                    if (is_null($username)) {
                        // Username not being sent
                        $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REMOTE');

                    } else {
                        $targetUser = $container->getUser();
                        if (!$username) {
                            // email in use, no username given yet
                            $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REMOTE');

                        } else {
                            try {
                                $targetUser->loadByUsername($username);

                                if ($targetUser->email != $email) {
                                    // Incorrect email for the selected user
                                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_INVALID');
                                }

                            } catch (Exception $e) {
                                // Email in use, no target user
                                $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REMOTE');
                            }
                        }
                    }

                }
            }
        }
        echo json_encode($message);
    }

    /**
     * Validate a password against a username if passed
     *
     * @return void
     * @throws Exception
     */
    public function password()
    {
        $this->checkToken();

        $app = SimplerenewFactory::getApplication();

        $message  = true;
        $password = $app->input->getString('password');
        $username = $app->input->getUsername('username');
        if ($username && $password) {
            $targetUser = SimplerenewFactory::getContainer()->getUser();
            try {
                $targetUser->loadByUsername($username);
                if (!$targetUser->validate($password)) {
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_NOMATCH');
                }

            } catch (Exception $e) {
                // User doesn't exist. This is good!
            }
        }

        echo json_encode($message);
    }

    /**
     * See if coupon is valid for at least one of the selected plans
     *
     * @return void
     * @throws Exception
     */
    public function coupon()
    {
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $filter    = JFilterInput::getInstance();

        $couponCode = $app->input->getString('coupon');

        $planCodes = $app->input->get('plans', array(), 'array');
        $planCodes = array_map(
            function ($code) use ($filter) {
                return $filter->clean($code, 'cmd');
            },
            $planCodes
        );

        $result = array(
            'valid'   => false,
            'coupon'  => null,
            'message' => 0,
            'error'   => null
        );

        try {
            $coupon = $container->getCoupon()->load($couponCode);

            $result['coupon'] = $coupon->getProperties();

            $discount = 0;
            $currency = null;
            foreach ($planCodes as $planCode) {
                $plan = $container->getPlan()->load($planCode);
                if ($coupon->isAvailable($plan)) {
                    $discount += $coupon->getDiscount($plan);
                    $currency = $currency ?: $plan->currency;

                    $result['valid'] = true;
                }
            }
            $discount = JHtml::_('currency.format', $discount, $currency);

            $result['message'] = JText::sprintf('COM_SIMPLERENEW_COUPON_PLAN_DISCOUNT', $discount);
            if (!$result['valid']) {
                $result['error'] = JText::plural('COM_SIMPLERENEW_ERROR_COUPON_UNAVAILABLE', count($planCodes));
            }

        } catch (NotFound $e) {
            $result['error'] = JText::_('COM_SIMPLERENEW_ERROR_COUPON_INVALID');

        } catch (Exception $e) {
            $result['error'] = JText::sprintf('COM_SIMPLERENEW_ERROR_COUPON_LOOKUP', $e->getCode());
        }

        echo json_encode($result);
    }
}
