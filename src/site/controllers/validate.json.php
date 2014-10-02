<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerValidate extends SimplerenewControllerJson
{
    public function username()
    {
        $this->checkToken();

        $user = SimplerenewFactory::getUser();
        if ($username = SimplerenewFactory::getApplication()->input->getUsername('username')) {
            $db = SimplerenewFactory::getDbo();

            $db->setQuery('Select id From #__users Where username=' . $db->quote($username));
            $id = $db->loadResult();
            if ($id && $id == $user->id) {
                // Current user requested, so we're cool
                $id = 0;
            }
        }

        echo json_encode(empty($id));
    }

    public function email()
    {
        $this->checkToken();

        $user = SimplerenewFactory::getUser();
        if ($email = SimplerenewFactory::getApplication()->input->getString('email')) {
            $db = SimplerenewFactory::getDbo();

            $db->setQuery('Select id From #__users Where email=' . $db->quote($email));
            $id = $db->loadResult();
            if ($id && $id == $user->id) {
                $id = 0;
            }
        }

        echo json_encode(empty($id));
    }

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
                $plan   = $container->getPlan()->load($planCode);
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

        } catch (Simplerenew\Exception\NotFound $e) {
            $result['error'] = JText::_('COM_SIMPLERENEW_ERROR_COUPON_INVALID');

        } catch (Exception $e) {
            $result['error'] = JText::sprintf('COM_SIMPLERENEW_ERROR_COUPON_LOOKUP', $e->getCode());
        }

        echo json_encode($result);
    }
}
