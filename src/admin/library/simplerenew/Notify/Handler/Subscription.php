<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Exception;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

class Subscription implements HandlerInterface
{
    /**
     * Execute a notify handler which should return a short string
     * explaining what was done if anything.
     *
     * @param Notify    $notice
     *
     * @return string
     */
    public function execute(Notify $notice)
    {
        $message = null;
        if (!empty($notice->user->id)) {
            $isNew = false;

            switch ($notice->action) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case Notify::ACTION_NEW:
                    $isNew = true;
                    // intentional fall-through

                case Notify::ACTION_UPDATE:
                case Notify::ACTION_RENEW:
                    $notice->user->addGroups($notice->subscription->plan);
                    $message = 'Update User Group';
                    break;

                case Notify::ACTION_EXPIRE:
                    $notice->user->removeGroups($notice->subscription->plan);
                    $message = 'Remove plan user group';
                    break;
            }

            // Did we do anything?
            if ($message) {
                $message = $notice->user->username . ': ' . $message;
                $notice
                    ->getContainer()
                    ->events
                    ->trigger('onSubscriptionAfterUpdate', array($notice->subscription, $isNew));
            }
        }
        return $message;
    }
}
