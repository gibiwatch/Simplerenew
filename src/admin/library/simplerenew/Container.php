<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Notify\Notify;
use Simplerenew\Plugin\Events;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Container
 *
 * @TODO    : This class could still use a lot of improvement!
 *
 * @package Simplerenew
 *
 * @property Account       $account
 * @property Billing       $billing
 * @property Configuration $configuration
 * @property Coupon        $coupon
 * @property Events        $events
 * @property Invoice       $invoice
 * @property Notify        $notify
 * @property Plan          $plan
 * @property Subscription  $subscription
 * @property User          $user
 *
 * @method Account       getAccount()
 * @method Billing       getBilling()
 * @method Configuration getConfiguration()
 * @method Coupon        getCoupon()
 * @method Events        getEvents()
 * @method Invoice       getInvoice()
 * @method Notify        getNotify()
 * @method Plan          getPlan()
 * @method Subscription  getSubscription()
 * @method User          getUser()
 */
class Container extends \Pimple\Container
{
    public function __construct($cms, $gateway, array $config = array())
    {
        parent::__construct();

        // Parameters
        $this['configData'] = $config;
        $this['cmsNamespace'] = $cms;
        $this['gatewayNamespace'] = $gateway;

        // Services
        $this['configuration'] = function ($c) {
            return new Configuration($c['configData']);
        };

        // User classes
        $this['userAdapter'] = function ($c) {
            $adapter = $c['cmsNamespace'] . '\User\Adapter';
            return new $adapter();
        };

        $this['user'] = $this->factory(function (Container $c) {
            return new User($c['configuration'], $c['userAdapter']);
        });

        // Events class
        $this['events'] = function ($c) {
            return new Events($c['configuration']);
        };

        // Gateway classes
        $this['account'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\AccountImp');
            return new Account($c['configuration'], $imp);
        });

        $this['billing'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\BillingImp');
            return new Billing($c['configuration'], $imp);
        });

        $this['coupon'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\CouponImp');
            return new Coupon($c['configuration'], $imp);
        });

        $this['invoice'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\InvoiceImp');
            return new Invoice($c['configuration'], $imp);
        });

        $this['notify'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\NotifyImp');
            return new Notify($c, $imp);
        });

        $this['plan'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\PlanImp');
            return new Plan($c['configuration'], $imp);
        });

        $this['subscription'] = $this->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\SubscriptionImp');
            return new Subscription($c['configuration'], $imp);
        });
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
     */
    public function getInstance($className)
    {
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
        return $instance;
    }

    /**
     * Find a service in the container based on short class name
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
