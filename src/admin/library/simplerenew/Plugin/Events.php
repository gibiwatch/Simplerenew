<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Plugin;

use Simplerenew\Configuration;
use Simplerenew\Exception;

defined('_JEXEC') or die();

class Events
{
    /**
     * @var array
     */
    protected $events = array();

    /**
     * @var CmsInterface
     */
    protected $cmsEvents = null;

    /**
     * @var array
     */
    protected $handlers = array();

    public function __construct(Configuration $config, CmsInterface $cmsEvents = null)
    {
        $this->configuration = $config;

        if ($events = $config->get('events')) {
            $this->registerEvents($events);
        }

        $this->cmsEvents = $cmsEvents;
    }

    /**
     * Register a handler for the selected events
     *
     * The array of events can be a string with a single event name
     *
     * @param object|string $className
     * @param array|string  $events
     *
     * @return void
     */
    public function registerHandler($className, $events)
    {
        if (is_object($className)) {
            $this->addHandler($className);
            $className = '\\' . trim(get_class($className), '\\');
        }

        if (strpos($className, '\\') !== 0) {
            $className = 'Simplerenew\\' . $className;
        }
        foreach ((array)$events as $event) {
            if (!isset($this->events[$event])) {
                $this->events[$event] = array();
            }
            if (array_search($className, $this->events[$event]) === false) {
                $this->events[$event][] = $className;
            }
        }
    }

    /**
     * Register events with specified handlers $events array form is:
     *
     * array('event' => array('className1'[, className2 ...]))
     *
     * The array of handlers can be a string with the class name of a single handler
     *
     * @param array $events
     *
     * @return void
     */
    public function registerEvents(array $events)
    {
        foreach ($events as $event => $handlers) {
            foreach ((array)$handlers as $handler) {
                $this->registerHandler($handler, $event);
            }
        }
    }

    /**
     * Raw event trigger. Returns results from all call backs
     *
     * @param string $event
     * @param array  $params
     *
     * @return array
     */
    public function trigger($event, array $params = array())
    {
        $results = array();
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $className) {
                if ($callable = $this->getCallable($className, $event)) {
                    $results[] = call_user_func_array($callable, $params);
                }
            }
        }
        if ($this->cmsEvents) {
            $results = array_merge($results, $this->cmsEvents->trigger($event, $params));
        }
        return $results;
    }

    /**
     * Trigger event and return true if all results evaluate to true
     *
     * @param string $event
     * @param array  $params
     *
     * @return bool
     */
    public function triggerTrue($event, array $params = array())
    {
        $results = $this->trigger($event, $params);
        foreach ($results as $result) {
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Trigger event and return true if all results evaluate to false
     *
     * @param string $event
     * @param array  $params
     *
     * @return bool
     */
    public function triggerFalse($event, array $params = array())
    {
        $results = $this->trigger($event, $params);
        foreach ($results as $result) {
            if ($result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return array|null
     */
    protected function getCallable($className, $method)
    {
        $key = md5($className);
        if (!isset($this->handlers[$key]) && class_exists($className)) {
            $this->addHandler(new $className(), $key);
        }

        if (isset($this->handlers[$key])) {
            $class    = $this->handlers[$key];
            $callable = array($class, $method);
            if (is_callable($callable)) {
                return $callable;
            }
        }

        return null;
    }

    /**
     * @param object $class
     * @param string $key
     *
     * @throws Exception
     */
    protected function addHandler($class, $key = null)
    {
        if (!is_object($class)) {
            throw new Exception('[' . get_class($this) . '] Attempt to register Invalid class');
        }

        $key = $key ?: md5('\\' . trim(get_class($class), '\\'));

        $this->handlers[$key] = $class;
    }
}
