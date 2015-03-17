<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

class Account implements HandlerInterface
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
        $response = null;
        if (!empty($notice->user->id)) {
            switch ($notice->action) {
                case Notify::ACTION_REACTIVATE:
                    $response = $notice->user->username . ' updated';
                    if (!$notice->user->enabled) {
                        $notice->user->enabled = true;
                        $response .= ': Re-enabled';
                    }

                    $oldGroups = array_values($notice->user->groups);
                    sort($oldGroups);

                    $notice->user->addGroups($notice->subscription->plan);
                    $newGroups = array_values($notice->user->groups);
                    sort($newGroups);

                    if ($oldGroups != $newGroups) {
                        $response .= ': ' . $notice->subscription->plan;
                    }
                    $notice->user->update();

                    return $response;
            }
        }
        return $response;
    }
}
