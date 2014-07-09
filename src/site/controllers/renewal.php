<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerRenewal extends SimplerenewControllerBase
{
    public function cancel()
    {
        $this->checkToken();

        $app       = SimplerenewFactory::getApplication();
        $id        = $app->input->getString('id');
        $container = SimplerenewFactory::getContainer();

        try {
            $user          = $container->getUser()->load();
            $account       = $container->getAccount()->load($user);
            $subscriptions = $container->getSubscription()->getList($account);

            if (!isset($subscriptions[$id])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_NOTAUTH'));
            }

            $subscriptions[$id]->cancel();

        } catch (NotFound $e) {
            return $this->callerReturn(
                JText::_('COM_SIMPLERENEW_WARN_RENEWAL_CANCEL_NOTFOUND'),
                'notice'
            );

        } catch (Exception $e) {
            return $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL_CANCEL', $e->getMessage()),
                'error'
            );
        }

        $this->callerReturn(JText::_('COM_SIMPLERENEW_RENEWAL_CANCEL_SUCCESS'));
    }
}
