<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
        /** @var SimplerenewModelRenewal $model */
        $model = $this->getModel();
        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $this->setLayout('login');
            } else {
                $this->subscriptions = $model->getSubscriptions();
            }
        } catch (Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }
}
