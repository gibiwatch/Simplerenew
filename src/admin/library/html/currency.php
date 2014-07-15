<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlCurrency
{
    /**
     * Format a number as currency
     * @TODO: enhance to accept differing currencies
     *
     * @param $amount
     *
     * @return string
     */
    public static function format($amount)
    {
        return '$' . number_format($amount, 2);
    }
}
