<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Exception;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

class Subscription implements HandlerInterface
{
    /**
     * @param Notify $notice
     *
     * @return string
     */
    public function execute(Notify $notice)
    {
        if ($notice->user->id) {
            switch ($notice->action) {
                case Notify::ACTION_NEW:
                case Notify::ACTION_UPDATE:
                case Notify::ACTION_RENEW:
                    $plan = $notice->getContainer()
                        ->getPlan()
                        ->load($notice->subscription->plan);
                    $notice->user->setGroup($plan);
                    return $notice->user->username . ': Update User Group';
                    break;

                case Notify::ACTION_EXPIRE:
                    $notice->user->setGroup();
                    return $notice->user->username . ': Remove plan user group';
                    break;
            }
        }
        return null;
    }
}
