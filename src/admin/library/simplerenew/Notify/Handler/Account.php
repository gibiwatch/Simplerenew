<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

class Account implements HandlerInterface
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
                case Notify::ACTION_REACTIVATE:
                    $notice->user->addGroups($notice->subscription->plan);
                    $notice->user->enabled = true;
                    $notice->user->update();
                    return $notice->user->username . ': Enabled';
            }
        }
        return null;
    }
}
