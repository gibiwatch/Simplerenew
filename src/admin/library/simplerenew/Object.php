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

    /**
     * Retrieve all public properties and their values
     * Although this duplicates get_object_vars(), it
     * is mostly useful for internal calls when we need
     * to filter out the non-public properties
     *
     * @return array
     */
    public function getProperties()
    {
        $properties = $this->getReflection()->getProperties(\ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($properties as $property) {
            $name = $property->name;
            $result[$name] = $this->$name;
        }
        return $result;
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

    public function __toString()
    {
        return get_class($this);
    }
}
