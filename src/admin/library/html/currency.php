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
        'AUD' => array('language' => 'en_AU', 'format' => '$%s'),
        'CAD' => array('language' => 'en_CA', 'format' => '$%s'),
        'EUR' => array('language' => 'de_DE', 'format' => '€%s'),
        'GBP' => array('language' => 'en_GB', 'format' => '£%s'),
        'USD' => array('language' => 'en_US', 'format' => '$%s'),

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

        setLocale(LC_NUMERIC, $selected['language']);
        $formats = localeconv();

        return sprintf(
            $selected['format'],
            number_format($amount, 2, $formats['decimal_point'], $formats['thousands_sep'])
        );
    }
}
