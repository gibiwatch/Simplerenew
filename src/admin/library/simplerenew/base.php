<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Api\AdapterInterface;

defined('_JEXEC') or die();

abstract class Base
{
    /**
     * @var string The expected adapter class needs to be set in subclasses
     */
    protected $adapterClass = null;

    /**
     * @var Configuration
     */
    protected $configuration = null;

    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * @var \ReflectionObject
     */
    protected $reflection = null;

    public function __construct(AdapterInterface $adapter = null, Configuration $config=null)
    {
        if ($this->adapterClass === null) {
            $parts = explode('\\', get_class($this));
            $this->adapterClass = array_pop($parts);
        }

        if ($adapter instanceof AdapterInterface) {
            $this->setAdapter($adapter);
        }

        if ($config instanceof Configuration) {
            $this->configuration = $config;
        }
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        if (!$this->configuration instanceof Configuration) {
            $this->configuration = new Configuration();
        }
        return $this->configuration;
    }

    /**
     * Set the gateway adapter and test for validity for subclass
     *
     * @param AdapterInterface $adapter
     *
     * @return AdapterInterface
     * @throws Exception
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $apiClass = '\\Simplerenew\\Api\\' . $this->adapterClass . 'Adapter';

        echo $apiClass;
        if (is_a($adapter, $apiClass)) {
            $this->adapter = $adapter;
            return $this->adapter;
        }

        throw new Exception('Invalid gateway adapter given - ' . get_class($adapter));
    }

    /**
     * Load a gateway adapter based on the instantiated subclass
     *
     * @return AdapterInterface
     * @throws Exception
     */
    public function getAdapter()
    {
        if (!$this->adapter instanceof AdapterInterface) {
            $className = $this->getAdapterName();
            if (!class_exists($className)) {
                throw new Exception("Adapter class {$className} was not found");
            }
            $this->adapter = new $className();
        }

        return $this->adapter;
    }

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
     * Get the name of the gateway adapter expected for the instantiated subclass
     *
     * @return null|string
     */
    private function getAdapterName()
    {
        if( $gateway = ucfirst($this->getConfiguration()->get('gateway.selected'))) {
            return '\\Simplerenew\\Gateway\\' . ucfirst($gateway) . '\\' . $this->adapterClass;
        }
        return null;
    }

    /**
     * Get a reflection object for $this
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
}
