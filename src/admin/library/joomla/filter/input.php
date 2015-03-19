<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.filter.input');

class SimplerenewFilterInput extends JFilterInput
{
    protected static $instances = array();

    /**
     * @param array $tagsArray
     * @param array $attrArray
     * @param int   $tagsMethod
     * @param int   $attrMethod
     * @param int   $xssAuto
     *
     * @return SimplerenewFilterInput
     */
    public static function &getInstance(
        $tagsArray = array(),
        $attrArray = array(),
        $tagsMethod = 0,
        $attrMethod = 0,
        $xssAuto = 1
    ) {
        $sig = md5(serialize(array($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto)));

        if (empty(static::$instances[$sig])) {
            static::$instances[$sig] = new static($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto);
        }
        return static::$instances[$sig];
    }

    public function clean($source, $type = 'string')
    {
        switch (strtoupper($type)) {
            case 'ARRAY_KEYS':
                if (is_array($source)) {
                    $result = $this->cleanArray($source);
                } else {
                    $result = $this->_remove($this->_decode($value));
                }
                break;

            default:
                $result = parent::clean($source, $type);
                break;
        }

        return $result;
    }

    /**
     * Filter an array and its keys to strings. Will recognize a key
     * of 'username' and use the username filter
     *
     * @param $source
     *
     * @return array
     */
    protected function cleanArray(array $source)
    {
        $result = array();
        foreach ($source as $key => $value) {
            $key = $this->_remove($this->_decode($key));
            if (is_string($value)) {
                $filter       = ($key == 'username' ? 'username' : 'string');
                $result[$key] = $this->clean($value, $filter);
            } else {
                $result[$key] = $this->cleanArray($value);
            }
        }
        return $result;
    }
}
