<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\AbstractLogger;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Api\Transaction;
use Simplerenew\Exception\NotFound;
use Simplerenew\Notify\Notify;
use Simplerenew\Plugin\Events;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Container
 *
 * @package Simplerenew
 *
 * @property Account        account
 * @property Billing        billing
 * @property string         cmsNamespace
 * @property Configuration  configuration
 * @property Coupon         coupon
 * @property Events         events
 * @property string         gateway
 * @property Invoice        invoice
 * @property AbstractLogger logger
 * @property Notify         notify
 * @property Plan           plan
 * @property Subscription   subscription
 * @property Transaction    transaction
 * @property User           user
 *
 * @method Account        getAccount()
 * @method Billing        getBilling()
 * @method Configuration  getConfiguration()
 * @method Coupon         getCoupon()
 * @method Events         getEvents()
 * @method Invoice        getInvoice()
 * @method AbstractLogger getLogger()
 * @method Notify         getNotify()
 * @method Plan           getPlan()
 * @method Subscription   getSubscription()
 * @method Transaction    getTransaction()
 * @method User           getUser()
 */
class Container extends \Pimple\Container
{
    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return null;
    }

    public function __call($name, $args)
    {
        if (strpos($name, 'get') === 0 && !$args) {
            $key = strtolower(substr($name, 3));
            if (isset($this[$key])) {
                return $this[$key];
            }
        }
        return null;
    }

    /**
     * Get instance of a class using parameter autodetect
     *
     * @param $className
     *
     * @return object
     * @throws NotFound
     */
    public function getInstance($className)
    {
        try {
            $class = new \ReflectionClass($className);
            if ($instance = $this->getServiceEntry($class)) {
                return $instance;
            }

            $dependencies = array();
            if (!is_null($class->getConstructor())) {
                $params = $class->getConstructor()->getParameters();
                foreach ($params as $param) {
                    $dependentClass = $param->getClass();
                    if ($dependentClass) {
                        $dependentClassName  = $dependentClass->name;
                        $dependentReflection = new \ReflectionClass($dependentClassName);
                        if ($dependentReflection->isInstantiable()) {
                            //use recursion to get dependencies
                            $dependencies[] = $this->getInstance($dependentClassName);
                        } elseif ($dependentReflection->isInterface()) {
                            // Interfaces need to be pre-registered in the container
                            if ($concrete = $this->getServiceEntry($dependentReflection, true)) {
                                $dependencies[] = $concrete;
                            }
                        }
                    }
                }
            }

            $instance = $class->newInstanceArgs($dependencies);

        } catch (\Exception $e) {
            throw new NotFound($e->getMessage(), 404, $e);
        }

        return $instance;
    }

    /**
     * Find a service in the container based on class name
     * Classes can be registered either through their short name
     * or full class name. Short name take precedence.
     *
     * @param \ReflectionClass $class
     * @param bool             $require
     *
     * @return object|null
     * @throws Exception
     */
    protected function getServiceEntry(\ReflectionClass $class, $require = false)
    {
        $key = strtolower($class->getShortName());
        if (isset($this[$key])) {
            return $this[$key];
        }

        $name = $class->getName();
        if (isset($this[$name])) {
            return $this[$name];
        }

        if ($require) {
            throw new Exception($class->getName() . ' -  is not registered in the container');
        }

        return null;
    }
}
