<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
        /**
         * @var SimplerenewModelAccount $model
         * @var Subscription            $subscription
         */

        if ($this->getLayout() == 'edit') {
            $this->enforceSSL();
        }

        $model = $this->getModel();

        $allowMultiple = $this->getParams()->get('basic.allowMultiple');
        $currentSubs   = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
        if ($allowMultiple) {
            // On multi-sub sites we won't be interested in expired subscriptions
            $model->setState('status.subscription', $currentSubs);
        }

        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $tpl = 'login';

            } else {
                $this->billing       = $model->getBilling();
                $this->subscriptions = $model->getSubscriptions();
            }

            if (!$allowMultiple && count($this->subscriptions)) {
                // Single sub sites only look at the most recent active subscriptions
                $filteredSubscriptions = array();
                foreach ($this->subscriptions as $id => $subscription) {
                    if ($subscription->status & $currentSubs) {
                        $filteredSubscriptions[$id] = $subscription;
                    }
                    $this->subscriptions = $filteredSubscriptions;
                }
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

    /**
     * Test if the requested editing form should be displayed
     *
     * @param string $name
     *
     * @return bool|string[]
     */
    protected function showForm($name = null)
    {
        $options = array_filter(explode(',', $this->getParams()->get('editForms')));

        if ($name === null) {
            return $options;
        }

        return !$options || in_array(strtolower($name), $options);
    }
}
