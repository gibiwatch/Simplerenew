<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewRenewal extends SimplerenewViewSite
{
    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var array
     */
    protected $subscriptions = array();

    /**
     * @var Simplerenew\Api\Subscription
     */
    protected $subscription = null;

    /**
     * @var Simplerenew\Api\Plan
     */
    protected $plan = null;

    public function display($tpl = null)
    {
        /** @var SimplerenewModelRenewal $model */
        $model = $this->getModel();
        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $this->setLayout('login');
            } else {
                $this->subscriptions = $model->getSubscriptions();
            }
        } catch (Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }

    /**
     * Calculate the total discount for the requested subscription ids
     *
     * @param string $coupon
     * @param array  $ids
     *
     * @return float|int
     * @throws Exception
     */
    protected function getDiscount($coupon, array $ids)
    {
        $discount = 0;
        if ($coupon && $ids) {
            try {
                $container = SimplerenewFactory::getContainer();
                $coupon    = $container->coupon->load($coupon);

                $plans = array();
                foreach ($ids as $id) {
                    $subscription = $container->subscription->load($id);
                    $plans[]      = $container->plan->load($subscription->plan);
                }
                $discount = $coupon->getDiscount($plans);

            } catch (\Simplerenew\Exception\NotFound $e) {
                // coupon or subscription not found
            }
        }

        return $discount;
    }
}
