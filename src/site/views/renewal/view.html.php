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
    protected $subscriptions = null;

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

        if ($this->getParams()->get('basic.allowMultiple')) {
            $this->setLayout('multiple');

        } elseif ($this->subscriptions) {
            // convert to integer keys for single sub sites
            $this->subscriptions = (array_values($this->subscriptions));

            switch ($this->subscriptions[0]->status) {
                case Subscription::STATUS_ACTIVE:
                    $this->setLayout('cancel');
                    break;

                case Subscription::STATUS_CANCELED:
                    $this->setLayout('reactivate');
                    break;

                case Subscription::STATUS_EXPIRED:
                    $this->setLayout('resubscribe');
                    break;
            }
        }

        parent::display($tpl);
    }
}
