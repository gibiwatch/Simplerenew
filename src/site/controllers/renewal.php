<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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

        try {
            $messages = $this->changeRenewals($ids);
            $link     = SimplerenewRoute::get('account');
            $this->setRedirect(JRoute::_($link), join('<br/>', $messages));

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL', $e->getMessage()),
                'error'
            );
        }
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws Exception
     */
    protected function changeRenewals(array $ids)
    {
        /**
         * @var Subscription $subscription
         */
        $container = SimplerenewFactory::getContainer();
        $messages  = array();

        $user    = $container->getUser()->load();
        $account = $container->getAccount()->load($user);

        $subscriptions = $container
            ->getSubscription()
            ->getList($account, Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED);

        $cancel   = array_diff(array_keys($subscriptions), $ids);
        $activate = array_intersect($ids, array_keys($subscriptions));
        foreach ($subscriptions as $subscription) {
            if ($subscription->status == Subscription::STATUS_ACTIVE
                && in_array($subscription->id, $cancel)
            ) {
                $subscription->cancel();

                $plan       = $container->getPlan()->load($subscription->plan);
                $messages[] = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_CANCELED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );

            } elseif ($subscription->status == Subscription::STATUS_CANCELED
                && in_array($subscription->id, $activate)
            ) {
                $subscription->reactivate();

                $plan       = $container->getPlan()->load($subscription->plan);
                $messages[] = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_REACTIVATED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );

            }
        }

        if (!$messages) {
            $messages[] = JText::_('COM_SIMPLERENEW_RENEWAL_NO_CHANGE');
        }
        return $messages;
    }
}
