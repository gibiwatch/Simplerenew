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
            $model = $this->getModel();
            $currentSubs = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
            $model->setState('status.subscription', $currentSubs);
        }

        try {
            $this->user = $this->get('User');
            if (!$this->user) {
                $this->setLayout('login');

            } else {
                $this->billing       = $this->get('Billing');
                $this->subscriptions = $this->get('Subscriptions');
            }

        } catch (Simplerenew\Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        if (!$allowMultiple && count($this->subscriptions)) {
            // Single sub sites only look at the most recent subscription
            $this->subscriptions = array(array_shift($this->subscriptions));
        }

        parent::display($tpl);
    }
}
