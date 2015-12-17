<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Api;
use Simplerenew\Exception;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

class Subscription implements HandlerInterface
{
    /**
     * Execute a notify handler which should return a short string
     * explaining what was done if anything.
     *
     * @param Notify $notice
     *
     * @return string
     */
    public function execute(Notify $notice)
    {
        $message = null;
        if (!empty($notice->user->id)) {
            switch ($notice->action) {
                case Notify::ACTION_UPDATE:
                case Notify::ACTION_RENEW:
                case Notify::ACTION_EXPIRE:
                case Notify::ACTION_NEW:
                    $message = $notice->user->username . ': Verify User Groups';
                    $notice->user->resetGroups($notice->getAllContainers());

                    $event = $notice->getContainer()->events;
                    if ($notice->action == Notify::ACTION_NEW) {
                        $event->trigger(
                            'simplerenewSubscriptionAfterCreate',
                            array(
                                $notice->subscription,
                                $notice->plan,
                                $notice->account,
                                $notice->coupon
                            )
                        );
                    } else {
                        $event->trigger(
                            'simplerenewSubscriptionAfterUpdate',
                            array(
                                $notice->subscription,
                                $notice->plan
                            )
                        );
                    }
                    break;
            }
        }
        return $message;
    }
}
