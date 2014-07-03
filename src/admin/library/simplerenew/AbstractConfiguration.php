<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

/**
 * A simple class for handling layered variables
 *
 * Class AbstractConfiguration
 * @package Simplerenew
 */
abstract class AbstractConfiguration
{
    /**
     * @var array
     */
    protected $settings = null;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Confirm that the current configuration is valid
     *
     * @return bool
     */
    abstract public function isValid();

    /**
     * Translate dot notation into array keys
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (strpos($name, '.') === false) {
            return isset($this->settings[$name]) ? $this->settings[$name] : $default;
        }
        $levels = explode('.', $name);

        $value = & $this->settings;
        for ($i = 0; $i < count($levels) - 1; $i++) {
            $key = $levels[$i];
            if (is_array($value) && isset($value[$key])) {
                $value = & $value[$key];
            } elseif (is_object($value) && isset($value->$key)) {
                $value = $value->$key;
            } else {
                return $default;
            }
        }

        $key = $levels[$i];
        if (isset($value[$key])) {
            return $value[$key];
        }
        return $default;
    }

    /**
     * Save a dot notation key to the setting array
     *
     * @param string $name
     * @param mixed  $newValue
     *
     * @return mixed
     * @throws Exception
     */
    public function set($name, $newValue)
    {
        if (is_object($newValue) || is_array($newValue)) {
            throw new Exception('Object/Array is not a valid configuration value');
        }

        $oldValue = $this->get($name);

        if (strpos($name, '.') === false) {
            $this->settings[$name] = $newValue;
        } else {
            $keys = explode('.', $name);
            $tree = & $this->settings;
            for ($i = 0; $i < count($keys) - 1; $i++) {
                $key        = $keys[$i];
                $tree[$key] = array();
                $tree       = & $tree[$key];
            }

            $final        = array_pop($keys);
            $tree[$final] = $newValue;
        }
        return $oldValue;
    }
}
