<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlCurrency
{
    /**
     * @var array
     */
    protected static $currencies = array(
        'AUD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ','),
        'CAD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ','),
        'EUR' => array('format' => '€%s', 'decimal' => ',', 'thousands' => '.'),
        'GBP' => array('format' => '£%s', 'decimal' => '.', 'thousands' => ','),
        'USD' => array('format' => '$%s', 'decimal' => '.', 'thousands' => ',')
    );

    /**
     * @var array
     */
    protected static $options = null;

    /**
     * Format a number as currency
     *
     * @param float  $amount
     * @param string $currency
     *
     * @return string
     */
    public static function format($amount, $currency = 'USD')
    {
        if (isset(static::$currencies[$currency])) {
            $selected = static::$currencies[$currency];
        } else {
            $selected = static::$currencies['USD'];
        }

        return sprintf(
            $selected['format'],
            number_format($amount, 2, $selected['decimal'], $selected['thousands'])
        );
    }

    /**
     * Return array of currency objects for use in select lists, etc.
     *
     * @return array
     */
    public static function options()
    {
        if (static::$options === null) {
            static::$options = array();
            foreach (static::$currencies as $code => $settings) {
                $option    = array(
                    'value' => $code,
                    'text'  => $code
                );
                static::$options[] = (object)array_merge($option, $settings);
            }
        }
        return static::$options;
    }

}
