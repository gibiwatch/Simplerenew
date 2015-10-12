<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception\Duplicate;
use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerRenewal extends SimplerenewControllerBase
{
    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @var Subscription[]
     */
    protected $validSubscriptions = null;

    /**
     * @var JRegistry
     */
    protected $params = null;

    /**
     * @var Plan[]
     */
    protected $plans = array();

    public function display()
    {
        $this->checkToken();

        $this->callerReturn($this->getTask() . ' is under construction');
    }

    public function update()
    {
        $this->checkToken();

        $ids = $this->getIdsFromRequest();

        try {
            if ($link = $this->changeRenewals($ids)) {
                $this->setRedirect(JRoute::_($link));
            }

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL', $e->getMessage()),
                'error'
            );
        }
    }

    public function extendTrial()
    {
        $this->checkToken();

        $app = SimplerenewFactory::getApplication();

        $subscriptions = $this->getValidSubscriptions();
        $ids           = $this->getIdsFromRequest();
        $intervalDays  = $app->input->getInt('intervalDays');

        $success = array();
        $errors  = array();
        foreach ($ids as $id) {
            if (!isset($subscriptions[$id])) {
                $errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_NOAUTH_SUBSCRIPTION', $id);

            } else {
                $plan = $this->getPlan($subscriptions[$id]->plan);

                if (!$subscriptions[$id]->inTrial()) {
                    $errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_EXTEND_TRIAL_INVALID', $plan->name);

                } elseif ($intervalDays > 0) {
                    try {
                        $interval = new DateInterval("P{$intervalDays}D");
                        $subscriptions[$id]->extendTrial($interval);
                        $success[] = JText::sprintf(
                            'COM_SIMPLERENEW_EXTEND_TRIAL_SUCCESS',
                            $plan->name,
                            $subscriptions[$id]->trial_end->format('F j, Y')
                        );

                    } catch (Duplicate $e) {
                        $errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_EXTEND_TRIAL_DUPLICATE', $plan->name);

                    }
                }
            }
        }

        if ($success) {
            $app->enqueueMessage(join('<br/>', $success));
        }
        if ($errors) {
            $this->callerReturn($errors, 'error');
            return;
        }

        $link = SimplerenewRoute::get('account');
        $this->setRedirect(JRoute::_($link));
    }

    public function offerCoupon()
    {
        $this->checkToken();

        $app           = SimplerenewFactory::getApplication();
        $container     = SimplerenewFactory::getContainer();
        $ids           = $this->getIdsFromRequest();
        $subscriptions = $this->getValidSubscriptions();
        $couponCode    = $app->input->getString('coupon');

        try {
            $coupon = $container->coupon->load($couponCode);

        } catch (NotFound $e) {
            $this->callerReturn(
                JText::sprintf(
                    'COM_SIMPLERENEW_ERROR_COUPON_NOTFOUND',
                    $couponCode
                )
            );
            return;
        }

        $plans = array();
        foreach ($ids as $id) {
            if (isset($subscriptions[$id])) {
                $plan = $this->getPlan($subscriptions[$id]->plan);
                if ($coupon->isAvailable($plan)) {
                    $plans[$plan->code] = $plan->name;
                }
            }
        }

        if ($plans) {
            try {
                $account = $this->getAccount();
                $coupon->activate($account);

            } catch (Exception $e) {
                $this->callerReturn(
                    JText::_('COM_SIMPLERENEW_ERROR_FUNNEL_COUPON'),
                    'error'
                );
                return;

            }

            $link    = SimplerenewRoute::get('account');
            $message = JText::sprintf(
                'COM_SIMPLERENEW_FUNNEL_COUPON_APPLIED',
                $coupon->name,
                $coupon->amountAsString()
            );
            $this->setRedirect(JRoute::_($link), $message);
        }
    }

    public function cancel()
    {
        $this->checkToken();

        $subscriptions = $this->getValidSubscriptions();
        $ids           = $this->getIdsFromRequest();

        $cancel = array();
        foreach ($ids as $id) {
            if (isset($subscriptions[$id])) {
                $cancel[$id] = $subscriptions[$id];
            }
        }

        $app     = SimplerenewFactory::getApplication();
        $success = $this->cancelSubscriptions($cancel);
        if ($success) {
            foreach ($success as $subscription) {
                $plan    = $this->getPlan($subscription->plan);
                $message = JText::sprintf(
                    'COM_SIMPLERENEW_RENEWAL_CANCELED',
                    $plan->name,
                    $subscription->period_end->format('F, j, Y')
                );
                $app->enqueueMessage($message);
            }
            if ($link = $this->getParams()->get('feedback')) {
                $link = 'index.php?Itemid=' . (int)$link;
            }
        }

        if (empty($link)) {
            $link = SimplerenewRoute::get('account');
        }
        $this->setRedirect(JRoute::_($link));
    }

    /**
     * Get all existing subscriptions for the current user
     *
     * @return Subscription[]
     */
    protected function getValidSubscriptions()
    {
        if ($this->validSubscriptions === null) {
            $account = $this->getAccount();

            $this->validSubscriptions = SimplerenewFactory::getContainer()->subscription
                ->getList($account, Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED);
        }
        return $this->validSubscriptions;
    }

    /**
     * Reactivate/Cancel selected subscriptions
     *
     * Return redirect link or null if display is handled here
     *
     * @param array $ids
     *
     * @return string|null
     * @throws Exception
     */
    protected function changeRenewals(array $ids)
    {
        $app           = SimplerenewFactory::getApplication();
        $subscriptions = $this->getValidSubscriptions();
        $cancel        = array();
        $activate      = array();
        $returnLink    = null;
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
            if ($activated = $this->activateSubscriptions($activate)) {
                foreach ($activated as $subscription) {
                    $plan    = $this->getPlan($subscription->plan);
                    $message = JText::sprintf(
                        'COM_SIMPLERENEW_RENEWAL_REACTIVATED',
                        $plan->name,
                        $subscription->period_end->format('F, j, Y')
                    );
                    $app->enqueueMessage($message);
                }
                if ($itemid = $this->getParams()->get('redirect')) {
                    $returnLink = 'index.php?Itemid=' . (int)$itemid;
                }
            }

            // If funnels are enabled, funnel them!
            $funnel = SimplerenewHelper::getFunnel();
            if ($cancel && $funnel->get('enabled', false)) {
                $app->input->set('layout', 'cancel');

                $app->input->set('ids', array_keys($cancel));
                $this->display();
                return null;
            }

            if ($canceled = $this->cancelSubscriptions($cancel)) {
                foreach ($canceled as $subscription) {
                    $plan    = $this->getPlan($subscription->plan);
                    $message = JText::sprintf(
                        'COM_SIMPLERENEW_RENEWAL_CANCELED',
                        $plan->name,
                        $subscription->period_end->format('F, j, Y')
                    );
                    $app->enqueueMessage($message);
                }
                if ($itemid = $this->getParams()->get('feedback')) {
                    $returnLink = 'index.php?Itemid=' . (int)$itemid;
                }
            }
        }
        return $returnLink ?: SimplerenewRoute::get('account');
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
                $plan = $this->getPlan($subscription->plan);
                $app->enqueueMessage(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL_CANCEL', $plan->name),
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
                $plan = $this->getPlan($subscription->plan);
                $app->enqueueMessage(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_RENEWAL_REACTIVATE', $plan->name),
                    'error'
                );
            }
        }
        return $success;
    }

    /**
     * Get the current menu parameters
     *
     * @return JRegistry
     */
    protected function getParams()
    {
        if ($this->params === null) {
            if ($menu = SimplerenewFactory::getApplication()->getMenu()->getActive()) {
                $this->params = $menu->params;
            } else {
                $this->params = new JRegistry;
            }
        }
        return $this->params;
    }

    /**
     * Safely get a plan object
     *
     * @param $planCode
     *
     * @return Plan
     */
    protected function getPlan($planCode)
    {
        if (!isset($this->plans[$planCode])) {
            $this->plans[$planCode] = SimplerenewFactory::getContainer()->plan;

            try {
                $this->plans[$planCode]->load($planCode);

            } catch (Exception $e) {
                $this->plans[$planCode]->code = $planCode;
                $this->plans[$planCode]->name = $planCode;
            }
        }
        return $this->plans[$planCode];
    }

    /**
     * Most tasks expect an array of subscription ids. This will safely
     * load and sanitise them from the request
     *
     * @return array
     */
    protected function getIdsFromRequest()
    {
        $app    = SimplerenewFactory::getApplication();
        $filter = SimplerenewFilterInput::getInstance();
        $ids    = $filter->clean($app->input->get('ids', array(), 'array'), 'array_keys');

        return $ids;
    }

    /**
     * Get the membership account for the currently logged in user
     *
     * @return Account
     */
    protected function getAccount()
    {
        if ($this->account === null) {
            $userId        = SimplerenewFactory::getUser()->id;
            $this->account = SimplerenewFactory::getContainer()->account->loadByUserid($userId);
        }
        return $this->account;
    }
}
