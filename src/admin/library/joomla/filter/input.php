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
    public static function getInstance(
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
            case 'USER_ARRAY':
                $filter = $this;
                $result = array_map(
                    function ($row) use ($filter) {
                        $row = array_map(array($filter, 'clean'), (array)$row);
                        if (isset($row['username'])) {
                            $row['username'] = $filter->clean($row['username'], 'username');
                        }
                        return $row;
                    },
                    (array)$source
                );
                break;

            default:
                $result = parent::clean($source, $type);
                break;
        }

        return $result;
    }
}
