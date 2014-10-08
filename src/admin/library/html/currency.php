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
    protected static $currencies = array(
        'AUD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ','),
        'CAD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ','),
        'EUR' => array('format' => '€%s', 'decimal' => ',', 'thousands' => '.'),
        'GBP' => array('format' => '£%s', 'decimal' => '.', 'thousands' => ','),
        'USD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ',')
    );

    /**
     * Format a number as currency
     * @TODO: enhance to accept differing currencies
     *
     * @param float  $amount
     * @param string $currency
     *
     * @return string
     */
    public static function format($amount, $currency = 'USD')
    {
        if (isset(self::$currencies[$currency])) {
            $selected = self::$currencies[$currency];
        } else {
            $selected = self::$currencies['USD'];
        }

        return sprintf(
            $selected['format'],
            number_format($amount, 2, $selected['decimal'], $selected['thousands'])
        );
    }
}
