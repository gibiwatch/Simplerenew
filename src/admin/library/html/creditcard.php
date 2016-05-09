<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlCreditcard
{
    public static function mask($lastFour)
    {
        return $lastFour ? str_pad($lastFour, 16, '*', STR_PAD_LEFT) : '';
    }

    public static function expiration($month, $year)
    {
        return sprintf('%02d/%04d', $month, $year);
    }
}
