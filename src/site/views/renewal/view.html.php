<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
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

    protected function getDateDiff($specifier)
    {
        if (!empty($specifier) && preg_match('/(\d+)\s*([DMW])/', strtoupper($specifier), $limit)) {
            list(, $length, $unit) = $limit;
            if ($unit == 'W') {
                $length *= 7;
                $unit = 'D';
            }
            $dateDiff = new DateInterval(strtoupper('P' . $length . $unit));

            $limit = new DateTime();
            $limit->add($dateDiff);
            return $limit;
        }

        return null;
    }

    protected function getDiscount($funnel)
    {
        $ids = SimplerenewFactory::getApplication()->input->get('ids', array(), 'array');
        if ($ids && !empty($funnel->offerCoupon)) {
            $discount = 0;

            try {
                $container = SimplerenewFactory::getContainer();
                $coupon    = $container->coupon->load($funnel->offerCoupon);

                foreach ($ids as $id) {
                    $subscription = $container->subscription->load($id);
                    $plan         = $container->plan->load($subscription->plan);

                    $discount += $coupon->getDiscount($plan);
                }
                return $discount;

            } catch (\Simplerenew\Exception\NotFound $e) {
                // coupon or subscription not found
            }
        }

        return 0;
    }
}
