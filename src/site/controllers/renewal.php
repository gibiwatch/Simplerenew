<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;
use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

class SimplerenewControllerRenewal extends SimplerenewControllerBase
{
    public function update()
    {
        $this->checkToken();

        $app    = SimplerenewFactory::getApplication();
        $filter = JFilterInput::getInstance();

        $ids = array_map(
            function ($id) use ($filter) {
                return $filter->clean($id, 'string');
            },
            $app->input->get('ids', array(), 'array')
        );

        echo '<pre>';
        print_r($ids);
        echo '</pre>';

    }

    /**
     * Get a specific subscription that is valid
     * for the current user to edit
     *
     * @param $id
     *
     * @return null|Subscription
     */
    protected function getValidSubscription($id)
    {
        $container = SimplerenewFactory::getContainer();

        try {
            $user          = $container->getUser()->load();
            $account       = $container->getAccount()->load($user);
            $subscriptions = $container->getSubscription()->getList($account);

            if (!isset($subscriptions[$id])) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_NOAUTH'));
            }

            return $subscriptions[$id];

        } catch (NotFound $e) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_WARN_RENEWAL_NOTFOUND'),
                'notice'
            );

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL', $e->getMessage()),
                'error'
            );
        }

        return null;
    }
}
