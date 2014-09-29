<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Subscription;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewSubscribe extends SimplerenewViewSite
{
    /**
     * @var array
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
     * @var array
     */
    protected $subscriptions = array();

    /**
     * @var bool
     */
    protected $allowMultiple = false;

    public function display($tpl = null)
    {
        $this->enforceSSL();

        // Depending on user state, there may not be any plans to choose
        $this->plans = $this->get('Plans');
        if (!$this->plans) {
            $this->setLayout('noplans');
            parent::display($tpl);
            return;
        }

        try {
            if ($this->user = $this->get('User')) {
                $this->account       = $this->get('Account');
                $this->billing       = $this->get('billing');
                $this->subscriptions = $this->get('Subscriptions');
            }

        } catch (Exception $e) {
            // We don't care if they aren't logged in or don't have an account
        }

        // Set blank objects as needed
        $container           = SimplerenewFactory::getContainer();
        $this->user          = $this->user ?: $container->getUser();
        $this->account       = $this->account ?: $container->getAccount();
        $this->billing       = $this->billing ?: $container->getBilling();
        $this->subscriptions = $this->subscriptions ?: array();

        // Fill in data from previous form attempt if any
        if ($formData = SimplerenewHelper::loadFormData('subscribe')) {
            $this->user->setProperties($formData);

            $this->account->setProperties($formData);
            if (!empty($formData['billing'])) {
                $this->billing->setProperties($formData['billing']);
            }
        }

        if (!empty($formData['planCode'])) {
            // Plans selected on last form submit
            $selectedPlans = array_fill_keys((array)$formData['planCode'], true);
        } elseif (!$this->subscriptions) {
            // By default select the first shown plan
            $plan          = current($this->plans);
            $selectedPlans = array($plan->code => true);
        }
        // Load current active/canceled subscriptions
        foreach ($this->subscriptions as $subscription) {
            $selectedPlans[$subscription->plan] = $subscription->id;
        }

        $this->allowMultiple = $this->getParams()->get('basic.allowMultiple');
        foreach ($this->plans as $plan) {
            $plan->subscription = empty($selectedPlans[$plan->code]) ? null : $selectedPlans[$plan->code];
            if ($plan->subscription === true) {
                $plan->selected = true;
            } elseif ($this->allowMultiple) {
                if (empty($this->subscriptions[$plan->subscription])) {
                    $plan->selected = false;
                } else {
                    $subscription   = $this->subscriptions[$plan->subscription];
                    $plan->selected = (bool)($subscription->status & Subscription::STATUS_ACTIVE);
                }
            } else {
                $plan->selected = !empty($this->subscriptions[$plan->subscription]);
            }
        }



        parent::display($tpl);
    }
}
