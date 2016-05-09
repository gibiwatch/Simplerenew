<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

abstract class JHtmlSubscription
{
    protected static $statusCode = array(
        Subscription::STATUS_ACTIVE   => 'COM_SIMPLERENEW_OPTION_STATUS_ACTIVE',
        Subscription::STATUS_CANCELED => 'COM_SIMPLERENEW_OPTION_STATUS_CANCELED',
        Subscription::STATUS_EXPIRED  => 'COM_SIMPLERENEW_OPTION_STATUS_EXPIRED'
    );

    public static function status($statusCode)
    {
        if (isset(static::$statusCode[$statusCode])) {
            $text = static::$statusCode[$statusCode];
        } else {
            $text = 'COM_SIMPLERENEW_OPTION_STATUS_UNKNOWN';
        }
        return JText::_($text);
    }
}
