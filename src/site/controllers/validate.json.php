<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
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

        $username = $app->input->getUsername('username');
        if ($username) {
            try {
                $user->loadByUsername($username);
                $currentUser->load();

            } catch (Exception $e) {
                // Really don't care at this point
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
                // Guest users can enter email to authenticate
                $email = $app->input->getString('email');

                if (is_null($email)) {
                    // email is not being sent
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_REMOTE');

                } elseif (!$email || $user->email != $email) {
                    // Entered email doesn't match
                    $message = JText::sprintf(
                        'COM_SIMPLERENEW_VALIDATE_TOOLTIP',
                        JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_VERIFY'),
                        JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_VERIFY_TOOLTIP')
                    );
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

        $email = $app->input->getString('email');
        if ($email) {
            try {
                $user->loadByEmail($email);
                $currentUser->load();

            } catch (Exception $e) {

            }
        }

        $message = true;
        if (!empty($user->id)) {
            if ($currentUser->id) {
                // Logged in users can only access their own account
                if ($currentUser->id != $user->id) {
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_NOAUTH');
                }

            } else {
                // Guest users can enter username to authenticate
                $username = $app->input->getUsername('username');

                if (is_null($username)) {
                    // username is not being sent
                    $message = JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REMOTE');

                } elseif (!$username || strcasecmp($user->username, $username)) {
                    // Entered username doesn't match
                    $message = JText::sprintf(
                        'COM_SIMPLERENEW_VALIDATE_TOOLTIP',
                        JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_VERIFY'),
                        JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_VERIFY_TOOLTIP')
                    );
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

        $app  = SimplerenewFactory::getApplication();
        $user = SimplerenewFactory::getContainer()->getUser();

        $password = $app->input->getString('password');
        $username = $app->input->getUsername('username');
        $email    = $app->input->getString('email');

        $message = true;
        if ($password && $username && $email) {
            try {
                $user->loadByUsername($username);
                if ($user->email == $email && !$user->validate($password)) {
                    $message = JText::sprintf(
                        'COM_SIMPLERENEW_VALIDATE_TOOLTIP',
                        JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_NOMATCH'),
                        JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_NOMATCH_TOOLTIP')
                    );
                }

            } catch (Exception $e) {
                // No user is just fine
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
        $this->checkToken();

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

    public function pricing()
    {
        $this->checkToken();

        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $filter    = SimplerenewFilterInput::getInstance();

        $couponCode = $app->input->getString('coupon');
        $coupon     = $container->coupon;
        if ($couponCode) {
            try {
                $coupon->load($couponCode);
            } catch (Exception $e) {
                // no problem - just return all current plan prices
            }
        }

        $planCodes = $app->input->get('plans', array(), 'array');
        $planCodes = array_map(
            function ($code) use ($filter) {
                return $filter->clean($code, 'cmd');
            },
            $planCodes
        );
        $plans     = $container->plan->getList();

        $result = array();
        /**
         * @var string                $planCode
         * @var \Simplerenew\Api\Plan $plan
         */
        foreach ($plans as $planCode => $plan) {
            $price             = $plan->getProperties();
            $price['discount'] = $coupon->getDiscount($plan);
            $price['coupon']   = $couponCode;

            $result[$planCode] = $price;
        }

        echo json_encode($result);
    }
}
