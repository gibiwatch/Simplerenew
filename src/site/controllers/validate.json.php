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

        $couponCode = $app->input->getString('coupon');
        $planCode   = $app->input->getCmd('plan');

        $result = array(
            'valid'    => false,
            'discount' => 0,
            'error'    => null
        );

        try {
            $coupon = $container->getCoupon()->load($couponCode);
            $plan   = $container->getPlan()->load($planCode);

            $discount = '$' . number_format($coupon->getDiscount($plan), 2);

            $result['valid']    = $coupon->isAvailable($plan);
            $result['discount'] = JText::sprintf('COM_SIMPLERENEW_COUPON_PLAN_DISCOUNT', $discount);
            if (!$result['valid']) {
                $result['error'] = JText::_('COM_SIMPLERENEW_ERROR_COUPON_UNAVAILABLE');
            }

        } catch (Simplerenew\Exception\NotFound $e) {
            $result['error'] = JText::_('COM_SIMPLERENEW_ERROR_COUPON_INVALID');

        } catch (Exception $e) {
            $result['error'] = JText::sprintf('COM_SIMPLERENEW_ERROR_COUPON_LOOKUP', $e->getCode());
        }

        echo json_encode($result);
    }
}
