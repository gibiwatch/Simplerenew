<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

class Object
{
    /**
     * @var \ReflectionObject
     */
    private $reflection = null;

    private $properties = null;

    /**
     * Retrieve all public properties and their values
     * Although this duplicates get_object_vars(), it
     * is mostly useful for internal calls when we need
     * to filter out the non-public properties. Note
     * we cache the property names assuming the public
     * facing properties will not change in the lifetime
     * of the object
     *
     * @return array
     */
    public function getProperties()
    {
        if ($this->properties === null) {
            $data = $this->getReflection()->getProperties(\ReflectionProperty::IS_PUBLIC);
            $this->properties = array();
            foreach ($data as $property) {
                $name = $property->name;
                $this->properties[$name] = $this->$name;
            }
        }
        return $this->properties;
    }

    /**
     * Set the public properties from the passed array/object
     *
     * @param mixed $data Associative array or object with properties to copy to $this
     *
     * @return $this
     * @throws Exception
     */
    public function setProperties($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (!is_array($data)) {
            throw new Exception('Invalid arguement given - ' . gettype($data));
        }

        $properties = $this->getProperties();
        foreach ($properties as $k => $v) {
            if (array_key_exists($k, $data)) {
                $this->$k = $data[$k];
            }
        }

        return $this;
    }

    /**
     * Get the reflection object for $this
     *
     * @return \ReflectionObject
     */
    private function getReflection()
    {
        if ($this->reflection === null) {
            $this->reflection = new \ReflectionObject($this);
        }
        return $this->reflection;
    }

    /**
     * @TODO: Refactor to non-Joomla cache class
     *
     * @return JCache
     */
    protected function getCache()
    {
        $cache = \JFactory::getCache('com_recurly.api', null);
        $cache->setCaching(true);
        return $cache;
    }

    /**
     * Default string rendering for the object. Subclasses should feel
     * free to override as desired.
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}
