<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

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

            // @TODO: Remove after testing done
            $js = <<<JSCRIPT
(function($) {
    $(document).ready(function() {
        $('form').on('submit', function(evt) {
            evt.preventDefault();
            alert('Under Construction');
        });
    });
})(jQuery);
JSCRIPT;

            SimplerenewFactory::getDocument()->addScriptDeclaration($js);
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
            $layout = 'default';
            if (count($result) == 1 && $this->funnel && $this->funnel->get('enabled')) {
                $subscription = current($result);
                if ($subscription->status == Subscription::STATUS_ACTIVE) {
                    $layout = 'cancel';
                }
            }
            $this->setLayout($layout);
        }
        return $result;
    }

    /**
     * Calculate the total discount for the requested subscription ids
     *
     * @param string         $coupon
     * @param Subscription[] $subscriptions
     *
     * @return float
     * @throws Exception
     */
    protected function getDiscount($coupon, array $subscriptions)
    {
        $discount = 0;
        if ($coupon && $subscriptions) {
            try {
                $container = SimplerenewFactory::getContainer();
                $coupon    = $container->coupon->load($coupon);

                $plans = array();
                foreach ($subscriptions as $subscription) {
                    $plans[] = $container->plan->load($subscription->plan);
                }
                $discount = $coupon->getDiscount($plans);

            } catch (NotFound $e) {
                // coupon or subscription not found
            }
        }
        return $discount;
    }
}
