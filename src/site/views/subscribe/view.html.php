<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewSubscribe extends SimplerenewViewSite
{
    /**
     * @var array
     */
    protected $plans = array();

    /**
     * @var Simplerenew\User\User
     */
    protected $user = null;

    /**
     * @var Simplerenew\Api\Account
     */
    protected $account = null;

    /**
     * @var Simplerenew\Api\Billing
     */
    protected $billing = null;

    public function display($tpl = null)
    {
        $app = SimplerenewFactory::getApplication();
        $container     = SimplerenewFactory::getContainer();
        $this->user    = $container->getUser();
        $this->account = $container->getAccount();
        $this->billing = $container->getBilling();

        try {
            $this->user->load();
            $this->account->load($this->user);
            $this->billing->load($this->account);
        } catch (Exception $e) {
            // We don't care if they aren't logged in or don't have an account
        }

        // Fill in data from previous form attempt if any
        if ($formData = SimplerenewHelper::loadFormData('subscribe.create', false)) {
            $this->user->setProperties($formData);
            $this->user->id = $formData['userid'];

            $this->account->setProperties($formData);
            if (!empty($formData['billing'])) {
                $this->billing->setProperties($formData['billing']);
            }
            $selectedPlan = $formData['planCode'];
        }

        $this->plans = $this->get('Plans');

        foreach ($this->plans as $plan) {
            $plan->selected = (!empty($selectedPlan) && $selectedPlan == $plan->code);
        }

        parent::display($tpl);
    }
}
