<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
     * @var Subscription
     */
    protected $subscription = null;

    /**
     * @var Plan
     */
    protected $plan = null;

    /**
     * @var Plan
     */
    protected $pending = null;

    public function display($tpl = null)
    {
        try {
            $this->user = $this->get('User');
            if (!$this->user) {
                $this->setLayout('login');

            } else {
                $this->billing      = $this->get('Billing');
                $this->subscription = $this->get('Subscription');
                $this->plan         = $this->get('Plan');
                $this->pending      = $this->get('Pending');
            }

        } catch (Simplerenew\Exception $e) {
            // @TODO: Decide what to do here, if anything
            SimplerenewFactory::getApplication()->enqueueMessage(
                'Houston, we have a problem: ' . $e->getMessage(),
                'error'
            );
            echo $e->getTraceMessage();
        }

        parent::display($tpl);
    }
}
