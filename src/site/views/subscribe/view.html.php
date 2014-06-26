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
        $container     = SimplerenewFactory::getContainer();
        $this->user    = $container->getUser()->load();
        $this->account = $container->getAccount();
        $this->billing = $container->getBilling();

        $this->plans = $this->get('Plans');

        foreach ($this->plans as $plan) {
            $plan->selected = false;
        }

        parent::display($tpl);
    }
}
