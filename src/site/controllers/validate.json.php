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

        $id       = -1;
        $user     = SimplerenewFactory::getUser();
        $username = SimplerenewFactory::getApplication()->input->getUsername('username');
        if ($username) {
            $db = SimplerenewFactory::getDbo();

            $query = $db->getQuery(true)
                ->select($db->quote('id'))
                ->from($db->quoteName('#__users'))
                ->where(
                    array(
                        $db->quoteName('username') . '=' . $db->quote($username),
                        $db->quoteName('id') . '!=' . $db->quote($user->id)
                    )
                );

            $id = $db->setQuery($query)->loadResult();
        }

        echo json_encode(empty($id));
    }

    /**
     * Check for existence of proposed new email address. For logged in users,
     * assume that this is an attempt to change their own email address. Blank
     * email address will validate as unavailable.
     *
     * @throws Exception
     */
    public function email()
    {
        $this->checkToken();

        $id    = -1;
        $user  = SimplerenewFactory::getUser();
        $email = SimplerenewFactory::getApplication()->input->getString('email');
        if ($email) {
            $db = SimplerenewFactory::getDbo();

            $query = $db->getQuery(true)
                ->Select($db->quoteName('id'))
                ->from($db->quoteName('#__users'))
                ->where(
                    array(
                        $db->quoteName('email') . '=' . $db->quote($email),
                        $db->quoteName('id') . '!=' . $db->quote($user->id)
                    )
                );

            $id = $db->setQuery($query)->loadResult();
        }

        echo json_encode(empty($id));
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
            'message' => 0,
            'error'   => null
        );

        try {
            $coupon = $container->getCoupon()->load($couponCode);

            $discount = 0;
            foreach ($planCodes as $planCode) {
                $plan = $container->getPlan()->load($planCode);
                if ($coupon->isAvailable($plan)) {
                    $discount += $coupon->getDiscount($plan);
                    $result['valid'] = true;
                }
            }
            $discount = '$' . number_format($discount, 2);

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
