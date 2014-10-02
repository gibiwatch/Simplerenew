<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
        try {
            $this->user = $this->get('User');
            if (!$this->user) {
                $this->setLayout('login');
            } else {
                $this->subscriptions = $this->get('Subscriptions');
            }
        } catch (Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }
}
