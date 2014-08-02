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
     * @return mixed
     */
    public function execute(Notify $notice)
    {
        $message = 'None';
        if ($notice->user->id) {
            switch ($notice->action) {
                case Notify::ACTION_NEW:
                case Notify::ACTION_UPDATE:
                case Notify::ACTION_RENEW:
                    $plan = $notice->getContainer()
                        ->getPlan()
                        ->load($notice->subscription->plan);
                    $notice->user->setGroup($plan);
                    $message = 'Update User Group';
                    break;

                case Notify::ACTION_EXPIRE:
                    $notice->user->setGroup();
                    $message = 'Remove plan user group';
                    break;
            }
        }

        return $message;
    }
}
