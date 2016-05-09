<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

require_once __DIR__ . '/account.php';

class SimplerenewModelRenewal extends SimplerenewModelAccount
{
    protected function populateState()
    {
        parent::populateState();

        // We're only interested in current subscriptions
        $currentSubs = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
        $this->setState('status.subscription', $currentSubs);
    }
}
