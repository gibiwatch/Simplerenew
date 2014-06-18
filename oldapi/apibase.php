<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class RecurlyApibase
{
    /**
     * @var string
     * Name of the class from the Recurly API. Should be overridden in the child classes
     */
    protected $classname = 'Recurly_Base';

    /**
     * @var Recurly_Base
     */
    protected $recurly = null;

    /**
     * We will accept either the appropriate id for the source
     * Recurly class, the source Recurly class, or $this
     *
     * @param mixed $id
     */
    public function __construct($id=null)
    {
        $classname = $this->classname;
        if ($id instanceof $classname) {
            $this->recurly = $id;
        } elseif ($id instanceof $this) {
            $this->recurly = $id->recurly;
        } elseif ($id != '') {
            $this->recurly = $classname::get($id);
        } else {
            $this->recurly = new $classname();
        }
    }

    /**
     * Pass an undefined method call to the Recurly class if properly loaded
     *
     * @param string $name
     * @param mixed  $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if ($this->recurly instanceof $this->classname) {
            return call_user_func_array(array($this->recurly, $name), $args);
        }
    }

    /**
     * Child classes can define getter methods for specialized
     * uses. Otherwise, if the correct Recurly class is loaded,
     * the property will be passed on to it. The special case
     * of 'recurly' will return the raw Recurly class
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if ($this->recurly instanceof $this->classname) {
            switch ($name) {
                case 'recurly':
                    return $this->recurly;
                    break;

                default:
                    return $this->recurly->$name;
                    break;
            }
        }
        return null;
    }

    /**
     * Set the value of a property in the raw recurly class
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value=null)
    {
        if ($this->recurly instanceof $this->classname) {
            $this->recurly->$name = $value;
        }
    }

    public function __toString()
    {
        return (string)$this->recurly;
    }

    /**
     * Whether a valid Recurly object of the type
     * required is loaded. Can and should be overridden
     * by child classes as needed.
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->recurly instanceof $this->classname);
    }

    /**
     * Load the requested Recurly item. If class matches $this->classname,
     * replaces the current object with it
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function load($id)
    {
        $classname = $this->classname;
        if ($id instanceof $classname) {
            $this->recurly = $id;
        } elseif ($id instanceof $this) {
            $this->recurly = $id->recurly;
        } else {
            $this->recurly = new $classname($id);
        }
        return $this;
    }

    /**
     * Provide for caching calls to the Recurly API. Overrides
     * cache settings in Joomla Global Config
     *
     * @return JCache
     */
    protected function getCache()
    {
        $cache = JFactory::getCache('com_recurly.api', null);
        $cache->setCaching(true);
        return $cache;
    }
}
