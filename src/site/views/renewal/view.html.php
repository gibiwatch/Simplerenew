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
     * @var JRegistry
     */
    protected $params = null;

    /**
     * @var Subscription
     */
    protected $subscription = null;

    public function display($tpl = null)
    {
        try {
            $this->user = $this->get('User');
            if (!$this->user) {
                $this->setLayout('login');
            } else {
                $this->subscription = $this->get('Subscription');
            }
        } catch (Exception $e) {
            // @TODO: Decide what to do here, if anything
            SimplerenewFactory::getApplication()->enqueueMessage(
                'Houston, we have a problem: ' . $e->getMessage(),
                'error'
            );
        }

        if ($this->subscription) {
            switch ($this->subscription->status) {
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

        $this->params = $this->getParams();

        parent::display($tpl);
    }
}
