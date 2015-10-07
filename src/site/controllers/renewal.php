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
            if ($this->changeRenewals($ids)) {
                $link = SimplerenewRoute::get('account');
                $this->setRedirect(JRoute::_($link));
            }

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL', $e->getMessage()),
                'error'
            );
        }
    }

    /**
     * Update subscriptions.
     *
     * Return true if completed, false other action has been taken
     *
     * @param array $ids
     *
     * @return bool
     * @throws Exception
     */
    protected function changeRenewals(array $ids)
    {
        /**
         * @var Subscription $subscription
         */
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();
        $user      = $container->getUser()->load();
        $account   = $container->getAccount()->load($user);

        $subscriptions = $container
            ->getSubscription()
            ->getList($account, Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED);

        $cancelCandidates   = array_diff(array_keys($subscriptions), $ids);
        $activateCandidates = array_intersect($ids, array_keys($subscriptions));

        // See if we have any changes to make
        $cancel     = array();
        $reactivate = array();
        foreach ($subscriptions as $subscription) {
            if ($subscription->status == Subscription::STATUS_ACTIVE
                && in_array($subscription->id, $cancelCandidates)
            ) {
                $cancel[$subscription->id] = $subscription;

            } elseif ($subscription->status == Subscription::STATUS_CANCELED
                && in_array($subscription->id, $activateCandidates)
            ) {
                $reactivate[$subscription->id] = $subscription;
            }
        }

        if (empty($cancel) && empty($reactivate)) {
            $app->enqueueMessage(JText::_('COM_SIMPLERENEW_RENEWAL_NO_CHANGE'));

        } else {
            foreach ($reactivate as $subscription) {
                $subscription->reactivate();

                $plan    = $container->getPlan()->load($subscription->plan);
                $message = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_REACTIVATED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );
                $app->enqueueMessage($message);
            }

            if ($cancel && SimplerenewHelper::getFunnel()->get('enabled', false)) {
                $app->input->set('layout', 'cancel');

                $app->input->set('ids', array_keys($cancel));
                $this->display();
                return false;
            }

            foreach ($cancel as $subscription) {
                $subscription->cancel();

                $plan    = $container->getPlan()->load($subscription->plan);
                $message = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_CANCELED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );
                $app->enqueueMessage($message);
            }
        }
        return true;
    }
}
