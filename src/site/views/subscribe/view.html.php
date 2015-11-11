<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewSubscribe extends SimplerenewViewSite
{
    /**
     * @var JRegistry
     */
    protected $state = null;

    /**
     * @var Plan[]
     */
    protected $plans = array();

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @var Billing
     */
    protected $billing = null;

    /**
     * @var Subscription[]
     */
    protected $subscriptions = array();

    /**
     * @var string
     */
    protected $requiredTag = '<span>*</span>';

    /**
     * @var bool
     */
    protected $allowMultiple = false;

    public function display($tpl = null)
    {
        $this->enforceSSL();

        $this->allowMultiple = $this->getParams()->get('basic.allowMultiple');

        /** @var SimplerenewModelSubscribe $model */
        $model = $this->getModel();

        $this->state = $model->getState();
        $this->plans = $model->getPlans();

        try {
            if ($this->user = $model->getUser()) {
                $this->account       = $model->getAccount();
                $this->billing       = $model->getBilling();
                $this->subscriptions = $model->getSubscriptions();
            }

        } catch (NotFound $e) {
            // We don't care if they aren't logged in or don't have an account
        }

        // Set blank objects as needed
        $container           = SimplerenewFactory::getContainer();
        $this->user          = $this->user ?: $container->getUser();
        $this->account       = $this->account ?: $container->getAccount();
        $this->billing       = $this->billing ?: $container->getBilling();
        $this->subscriptions = $this->subscriptions ?: array();

        // Depending on conditions, there may not be any plans to choose
        foreach ($this->subscriptions as $subscription) {
            if (isset($this->plans[$subscription->plan])) {
                unset($this->plans[$subscription->plan]);
            }
        }

        if (!$this->plans) {
            $this->setLayout('noplans');
            parent::display($tpl);
            return;
        }

        // Fill in data from previous form attempt if any
        if ($formData = SimplerenewHelper::loadFormData('subscribe')) {
            if (!empty($formData['usernameLogin'])) {
                $formData['username'] = $formData['usernameLogin'];
            }
            $this->user->setProperties($formData);

            $this->account->setProperties($formData);
            if (!empty($formData['billing'])) {
                $this->billing->setProperties($formData['billing']);
            }
        }

        // Determine which plans to show pre-selected
        $app           = SimplerenewFactory::getApplication();
        $overrides     = $app->input->getString('select');
        $selectedPlans = array();

        if (!empty($formData['planCodes'])) {
            // Plans selected on last form submit
            $selectedPlans = array_fill_keys((array)$formData['planCodes'], true);

        } elseif ($this->subscriptions) {
            // Load current active/canceled subscriptions
            foreach ($this->subscriptions as $subscription) {
                if (isset($this->plans[$subscription->plan])) {
                    $selectedPlans[$subscription->plan] = $subscription->id;
                }
            }

        }
        if (empty($selectedPlans)) {
            if (!$overrides && !$this->allowMultiple) {
                // By default select the first shown plan on single sub sites
                reset($this->plans);
                $plan          = current($this->plans);
                $selectedPlans = array($plan->code => true);

            } elseif ($overrides) {
                $planList = array_fill_keys(explode(' ', $overrides), true);
                if (!$this->allowMultiple) {
                    // On single sub sites, makes sure at least one plan will be selected
                    $planList = array_merge(
                        $planList,
                        array_slice($this->plans, 0, 1, true)
                    );
                }

                foreach ($planList as $planCode => $value) {
                    if (isset($this->plans[$planCode])) {
                        $selectedPlans[$planCode] = true;
                        if (!$this->allowMultiple) {
                            // For single sub sites, use only the first available one we can find
                            break;
                        }
                    }
                }
            }
        }

        // Collect all active/canceled subscriptions and add info to plans list
        foreach ($this->plans as $plan) {
            $plan->subscription = empty($selectedPlans[$plan->code]) ? null : $selectedPlans[$plan->code];
            if ($plan->subscription === true) {
                $plan->selected = true;
            } else {
                $plan->selected = !empty($this->subscriptions[$plan->subscription]);
            }
        }

        parent::display($tpl);
    }
}
