<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Events;

defined('_JEXEC') or die();

class Events
{
    /**
     * @var array
     */
    protected $events = array();

    /**
     * @var array
     */
    protected $handlers = array();

    public function __construct(array $events = array())
    {
        $this->registerEvents($events);
    }

    /**
     * @param string       $className
     * @param array|string $events
     *
     * @return void
     */
    public function register($className, $events)
    {
        foreach ((array)$events as $event) {
            if (!isset($this->events[$event])) {
                $this->events[$event] = array();
            }
            if (array_search($className, $this->events[$event]) === false) {
                $this->events[$event][] = $className;
            }
        }
    }

    public function registerEvents(array $events)
    {
        foreach ($events as $event => $handlers) {
            foreach ($handlers as $handler) {
                $this->register($handler, $event);
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
            $this->handlers[$key] = new $className();
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
}
