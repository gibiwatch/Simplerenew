<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Billing;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewAccount extends SimplerenewViewSite
{
    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var Billing
     */
    protected $billing = null;

    /**
     * @var array
     */
    protected $subscriptions = array();

    public function display($tpl = null)
    {
        if ($this->getLayout() == 'edit') {
            $this->enforceSSL();
        }

        $allowMultiple = $this->getParams()->get('basic.allowMultiple');
        if ($allowMultiple) {
            // On multi-sub sites we won't be interested in expired subscriptions
            $model       = $this->getModel();
            $currentSubs = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
            $model->setState('status.subscription', $currentSubs);
        }

        /** @var SimplerenewModelAccount $model */
        $model = $this->getModel();
        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $tpl = 'login';

            } else {
                $this->billing       = $model->getBilling();
                $this->subscriptions = $model->getSubscriptions();
            }

            if (!$allowMultiple && count($this->subscriptions)) {
                // Single sub sites only look at the most recent subscription
                $this->subscriptions = array(array_shift($this->subscriptions));
            }

        } catch (Simplerenew\Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }

    /**
     * Load a plan safely without throwing any errors
     *
     * @param string $code
     *
     * @return Plan
     */
    protected function getPlan($code)
    {
        /** @var Plan $plan */
        $plan = SimplerenewFactory::getContainer()->getPlan();
        try {
            $plan->load($code);

        } catch (Simplerenew\Exception\NotFound $e) {
            $error = JText::sprintf('COM_SIMPLERENEW_ERROR_PLAN_NOT_FOUND', $code);

        } catch (Exception $e) {
            $error = JText::sprintf('COM_SIMPLERENEW_ERROR_PLAN_LOAD', $code);
        }

        if (!empty($error)) {
            SimplerenewFactory::getApplication()
                ->enqueueMessage($error, 'error');

            $plan->code = $code;
            $plan->name = $code;
        }

        return $plan;
    }
}
