<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Class SimplerenewUtilitiesArrayHelper
 *
 * @method static array  arrayUnique($myArray)
 * @method static array  fromObject($p_obj, $recurse = true, $regex = null)
 * @method static array  getColumn(&$array, $index)
 * @method static mixed  getValue(&$array, $name, $default = null, $type = '')
 * @method static array  invert($array)
 * @method static bool   isAssociative($array)
 * @method static array  pivot($source, $key = null)
 * @method static mixed  sortObjects(&$a, $k, $direction = 1, $caseSensitive = true, $locale = false)
 * @method static array  toInteger(&$array, $default = null)
 * @method static object toObject(&$array, $class = 'stdClass', $recursive = true)
 * @method static string toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
 *
 */
abstract class SimplerenewUtilitiesArray
{
    public static function __callStatic($name, $arguments)
    {

        if (class_exists('\\Joomla\\Utilities\\ArrayHelper')) {
            return call_user_func_array(array('\Joomla\Utilities\ArrayHelper', $name), $arguments);

        } else {
            $array   = $arguments[0];
            $default = count($arguments) == 2 ? $arguments[1] : null;

            switch ($name) {
                case 'fromObject':
                    $recurse = count($arguments) < 2 ? true : $arguments[1];
                    $regex   = count($arguments) < 3 ? null : $arguments[2];
                    return JArrayHelper::fromObject($array, $recurse, $regex);

                case 'toObject':
                    $class = count($arguments) < 2 ? 'stdClass' : $arguments[1];
                    return JArrayHelper::toObject($array, $class);

                case 'sortObjects':
                    $key = $arguments[1];
                    $direction = count($arguments) < 3 ? 1 : $arguments[2];
                    $caseSensitive = count($arguments) < 4 ?  true : $arguments[3];
                    $locale = count($arguments) < 5 ? false : $arguments[4];
                    return JArrayHelper::sortObjects($array, $key, $direction, $caseSensitive, $locale);

                case 'getColumn':
                    $index = isset($arguments[1]) ? $arguments[1] : null;
                    return JArrayHelper::getColumn($array, $index);

                case 'getValue':
                    $name    = empty($arguments[1]) ? null : $arguments[1];
                    $default = empty($arguments[2]) ? null : $arguments[2];
                    $type    = empty($arguments[3]) ? '' : $arguments[3];

                    return JArrayHelper::getValue($array, $name, $default, $type);

                case 'toString':
                    $inner_glue   = empty($arguments[1]) ? '=' : $arguments[1];
                    $outer_glue   = !array_key_exists(2, $arguments) ? ' ' : $arguments[2];
                    $keepOuterKey = empty($arguments[3]) ? false : $arguments[3];

                    if (!$keepOuterKey || $outer_glue === null) {
                        $outer_glue = '';
                    }
                    return JArrayHelper::toString($array, $inner_glue, $outer_glue, true);

                case 'arrayUnique':
                case 'isAssociative':
                case 'pivot':
                    return JArrayHelper::$name($array, $default);
            }

            JArrayHelper::$name($array, $default);
            return $array;
        }
    }
}
