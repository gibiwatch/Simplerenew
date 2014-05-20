<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Configuration;
use Simplerenew\Exception;

defined('_JEXEC') or die();

abstract class AbstractGatewayBase
{
    abstract public function __construct(array $config = array());

    /**
     * Map values in a source object/array to Simplerenew keys using a map
     * of key equivalences. Any fields in $keys not present in $map will be
     * mapped name to name. Map fields mapped to null will be ignored.
     *
     * Special mappings for field values are recognized with another array. e.g.:
     *
     * $map['status'] = array(
     *     'state' => array(
     *         'active' => 1,
     *         'closed' => 0,
     *         '::'     => -1
     *     )
     * )
     * Will map the Simplerenew field 'status' to the source field 'state' and
     * set status based on the value in the state field. If no match, '::' will be used for
     * the unknown value.
     *
     *
     * @param mixed $source Object or associative array of source data to be mapped
     * @param array $keys   Simplerenew keys for which values are being requested
     * @param array $map    Associative array where key=Simplerenew Key, value=Source Key
     *
     * @return array
     * @throws \Simplerenew\Exception
     */
    protected function map($source, array $keys, array $map)
    {
        if (!is_object($source) && !is_array($source)) {
            throw new Exception('Expected array or object for source argument');
        }

        $result = array_fill_keys($keys, null);
        foreach ($keys as $srKey) {
            $value = null;
            if (isset($map[$srKey]) && is_array($map[$srKey])) {
                $values   = reset($map[$srKey]);
                $field    = key($map[$srKey]);
                $selected = is_object($source) ? $source->$field : $source[$field];
                if (isset($values[$selected])) {
                    $value = $values[$selected];
                } elseif (isset($values['::'])) {
                    $value = $values['::'];
                }
            } elseif (isset($map[$srKey]) && $map[$srKey] === null) {
                $value = null;
            } else {
                $field = isset($map[$srKey]) ? $map[$srKey] : $srKey;
                $value = is_object($source) ? $source->$field : $source[$field];
            }
            $result[$srKey] = $value;
        }

        return $result;
    }
}
