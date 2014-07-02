<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlDatetime
{
    public static function format($dateTime, $blank = '', $format = 'F j, Y')
    {
        if ($dateTime instanceof DateTime) {
            return $dateTime->format($format);
        }

        return $blank;
    }
}
