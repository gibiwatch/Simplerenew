<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

abstract class JHtmlDatetime
{
    protected static $intervals = array(
        'y' => 'YEARS',
        'm' => 'MONTHS',
        'w' => 'WEEKS',
        'd' => 'DAYS'
    );

    /**
     * Get a corrected text version of a more flexible, simplified date interval.
     * Anything in the format: n Text
     * Where n is an integer and Text could be anything starting with y, m, w or d.
     * Whitespace is optional.
     *
     * @param SRDateTime $start
     * @param string     $userInput
     *
     * @return string
     */
    public static function difference(SRDateTime $start, $userInput)
    {
        $end = clone $start;
        $end->addFromUserInput($userInput);

        $difference = $end->diff($start);

        if (preg_match('/\d+\s*([ymwd])/', strtolower($userInput), $match)) {
            $property = $match[1];

            $text = 'COM_SIMPLERENEW_N_' . static::$intervals[$property];
            if ($property == 'w') {
                $value = $difference->days / 7;
            } elseif ($property == 'm') {
                $value = ($difference->y * 12) + $difference->m;
            } else {
                $value = $difference->$property;
            }

            return JText::plural($text, $value);
        }
        return '';
    }
}
