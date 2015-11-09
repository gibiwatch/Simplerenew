<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Coupon;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewRenewal extends SimplerenewViewSite
{
    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var Subscription[]
     */
    protected $subscriptions = array();

    /**
     * @var Subscription
     */
    protected $subscription = null;

    /**
     * @var Plan
     */
    protected $plan = null;

    /**
     * @var JRegistry
     */
    protected $funnel = null;

    public function display($tpl = null)
    {
        /** @var SimplerenewModelRenewal $model */
        $model = $this->getModel();

        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $this->setLayout('login');
            } else {
                $this->funnel        = SimplerenewHelper::getFunnel($this->getParams());
                $this->subscriptions = $this->getSubscriptions();
            }
            parent::display($tpl);

        } catch (Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
    }

    /**
     * Depends on $this->funnel being previously set
     *
     * @return Subscription[]
     */
    protected function getSubscriptions()
    {
        /**
         * @var SimplerenewModelRenewal $model
         * @var Subscription            $subscription
         */
        $app    = SimplerenewFactory::getApplication();
        $result = array();

        $cancelIds = $app->input->get('ids', array(), 'array');
        if ($this->getLayout() == 'cancel' && $cancelIds) {
            // Already in a cancel layout and have ids to process
            $container = SimplerenewFactory::getContainer();

            $this->subscriptions = array();
            foreach ($cancelIds as $cancelId) {
                $result[$cancelId] = $container->subscription->load($cancelId);
            }

        } else {
            // Get all applicable subscriptions
            $model  = $this->getModel();
            $result = $model->getSubscriptions();
            if (count($result) == 1 && $this->funnel && $this->funnel->get('enabled')) {
                $subscription = current($result);
                if ($subscription->status == Subscription::STATUS_ACTIVE) {
                    $this->setLayout('cancel');
                }
            }
        }
        return $result;
    }

    /**
     * Verify that the selected coupon applies to at least one subscription
     *
     * @param string         $coupon
     * @param Subscription[] $subscriptions
     *
     * @return Coupon
     */
    protected function validateCoupon($coupon, array $subscriptions)
    {
        if ($coupon && $subscriptions) {
            try {
                $container = SimplerenewFactory::getContainer();
                $coupon    = $container->coupon->load($coupon);

                foreach ($subscriptions as $subscription) {
                    $planCode = $subscription->pending_plan ?: $subscription->plan;
                    $plan     = $container->plan->load($planCode);
                    if ($coupon->isAvailable($plan)) {
                        return $coupon;
                    }
                }

            } catch (NotFound $e) {
                // coupon or plan not found
            }
        }
        return null;
    }
}
