<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewControllerRenewal extends SimplerenewControllerBase
{
    /**
     * @var Subscription[]
     */
    protected $validSubscriptions = null;

    public function display()
    {
        $this->checkToken();

        $this->callerReturn($this->getTask() . ' is under construction');
    }

    public function update()
    {
        $this->checkToken();

        $app    = SimplerenewFactory::getApplication();
        $filter = SimplerenewFilterInput::getInstance();
        $ids    = $filter->clean($app->input->get('ids', array(), 'array'), 'array_keys');

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
     * Get all existing subscriptions for the current user
     *
     * @return Subscription[]
     */
    protected function getValidSubscriptions()
    {
        if ($this->validSubscriptions === null) {
            $container = SimplerenewFactory::getContainer();
            $user      = $container->getUser()->load();
            $account   = $container->getAccount()->load($user);

            $this->validSubscriptions = $container
                ->getSubscription()
                ->getList($account, Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED);
        }
        return $this->validSubscriptions;
    }

    /**
     * Reactivate/Cancel selected subscriptions
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
        $app           = SimplerenewFactory::getApplication();
        $subscriptions = $this->getValidSubscriptions();
        $cancel        = array();
        $activate      = array();
        foreach ($subscriptions as $subscription) {
            if (!in_array($subscription->id, $ids) && $subscription->status == Subscription::STATUS_ACTIVE) {
                $cancel[$subscription->id] = $subscription;

            } elseif (in_array($subscription->id, $ids) && $subscription->status == Subscription::STATUS_CANCELED) {
                $activate[$subscription->id] = $subscription;
            }
        }

        if (empty($cancel) && empty($activate)) {
            $app->enqueueMessage(JText::_('COM_SIMPLERENEW_RENEWAL_NO_CHANGE'));

        } else {
            $container = SimplerenewFactory::getContainer();

            $activated = $this->activateSubscriptions($activate);
            foreach ($activated as $subscription) {
                $plan    = $container->getPlan()->load($subscription->plan);
                $message = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_REACTIVATED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );
                $app->enqueueMessage($message);
            }

            // If funnels are enabled, funnel them!
            if ($cancel && SimplerenewHelper::getFunnel()->get('enabled', false)) {
                $app->input->set('layout', 'cancel');

                $app->input->set('ids', array_keys($cancel));
                $this->display();
                return false;
            }

            $canceled = $this->cancelSubscriptions($cancel);
            foreach ($canceled as $subscription) {
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

    /**
     * Cancel any active subscriptions in the selected list
     *
     * @param Subscription[] $cancel
     *
     * @return Subscription[]
     */
    protected function cancelSubscriptions(array $cancel)
    {
        $app           = SimplerenewFactory::getApplication();
        $subscriptions = $this->getValidSubscriptions();
        $success       = array();
        foreach ($cancel as $id => $subscription) {
            try {
                if (!empty($subscriptions[$id]) && $subscription->status == Subscription::STATUS_ACTIVE) {
                    $subscription->cancel();
                    $success[$id] = $subscription;
                }

            } catch (Exception $e) {
                try {
                    $plan = SimplerenewFactory::getContainer()->plan->load($subscription->plan);
                    $planName = $plan->name;

                } catch (NotFound $e) {
                    $planName = $subscription->plan;

                }
                $app->enqueueMessage(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL_CANCEL', $planName),
                    'error'
                );
            }
        }
        return $success;
    }

    /**
     * Reactivate any canceled subscriptions in the selected list
     *
     * @param Subscription[] $activate
     *
     * @return Subscription[]
     */
    protected function activateSubscriptions(array $activate)
    {
        $app           = SimplerenewFactory::getApplication();
        $subscriptions = $this->getValidSubscriptions();
        $success       = array();
        foreach ($activate as $id => $subscription) {
            try {
                if (!empty($subscriptions[$id]) && $subscription->status == Subscription::STATUS_CANCELED) {
                    $subscription->reactivate();
                    $success[$id] = $subscription;
                }

            } catch (Exception $e) {
                try {
                    $plan = SimplerenewFactory::getContainer()->plan->load($subscription->plan);
                    $planName = $plan->name;

                } catch (NotFound $e) {
                    $planName = $subscription->plan;
                }
                $app->enqueueMessage(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL_REACTIVATE', $planName),
                    'error'
                );
            }
        }
        return $success;
    }
}
