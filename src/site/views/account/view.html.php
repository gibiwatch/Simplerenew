<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewAccount extends SimplerenewViewSite
{
    /**
     * @var Simplerenew\User\User
     */
    protected $user = null;

    /**
     * @var Simplerenew\Api\Billing
     */
    protected $billing = null;

    /**
     * @var Simplerenew\Api\Subscription
     */
    protected $subscription = null;

    public function display($tpl = null)
    {
        $this->user = $this->get('User');
        $this->billing = $this->get('Billing');
        $this->subscription = $this->get('Subscription');

        parent::display($tpl);
    }
}
