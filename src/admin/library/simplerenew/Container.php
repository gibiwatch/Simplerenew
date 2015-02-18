<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Pimple\ServiceProviderInterface;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Gateway\CouponInterface;
use Simplerenew\Gateway\InvoiceInterface;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Gateway\PlanInterface;
use Simplerenew\Gateway\SubscriptionInterface;
use Simplerenew\Notify\Notify;
use Simplerenew\Plugin\Events;
use Simplerenew\Primitive\AbstractPayment;
use Simplerenew\Primitive\CreditCard;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Container
 *
 * @package Simplerenew
 *
 * @property AbstractPayment $paymentType
 * @property Account         $account
 * @property Billing         $billing
 * @property Configuration   $configuration
 * @property Coupon          $coupon
 * @property CreditCard      $creditcard
 * @property Events          $events
 * @property Invoice         $invoice
 * @property Notify          $notify
 * @property Plan            $plan
 * @property Subscription    $subscription
 * @property User            $user
 */
class Container extends \Pimple\Container
{
    public function __construct(array $config = array())
    {
        parent::__construct();

        $required = array(
            'configClass'     => 'Simplerenew\Configuration',
            'creditCardClass' => 'Simplerenew\Primitive\CreditCard'
        );
        $config   = array_merge($required, $config);

        // Parameters
        $this['configData']      = $config;
        $this['configClass']     = $config['configClass'];
        $this['creditCardClass'] = $config['creditCardClass'];

        $this['userAdapterClass'] = isset($config['user']['adapter']) ? $config['user']['adapter'] : null;

        // Services
        $this['configuration'] = function ($c) {
            return new $c['configClass']($c['configData']);
        };

        $this['userAdapter'] = function ($c) {
            $adapter = $c['userAdapterClass'];
            return new $adapter();
        };

        $this['user'] = $this->factory(function ($c) {
            $config = $c['configuration']->toConfig('user');
            return new User($config, $c['userAdapter']);
        });

        $this['events'] = function ($c) {
            $config = $c['configuration']->toConfig('events');
            return new Events($config);
        };
    }

    public function __get($name)
    {
        if ($this[$name]) {
            return $this[$name];
        }

        // @deprecated moving away from explicit getters
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * @return User
     * @deprecated as of v1.1 use property access instead
     */
    public function getUser()
    {
        return $this['user'];
    }

    /**
     * Create a new Account object
     *
     * @param AccountInterface $imp
     * @param Configuration    $config
     *
     * @return Account
     */
    public function getAccount(AccountInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('account');
        $imp    = $imp ?: $this->getGatewayImp('AccountImp');

        $account = new Account($config, $imp);
        return $account;
    }

    /**
     * @param BillingInterface $imp
     * @param Configuration    $config
     *
     * @return Billing
     */
    public function getBilling(BillingInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('billing');
        $imp    = $imp ?: $this->getGatewayImp('BillingImp');

        $billing = new Billing($config, $imp);
        return $billing;
    }

    /**
     * @param PlanInterface $imp
     * @param Configuration $config
     *
     * @return Plan
     */
    public function getPlan(PlanInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('plan');
        $imp    = $imp ?: $this->getGatewayImp('PlanImp');

        $plan = new Plan($config, $imp);
        return $plan;
    }

    /**
     * @param CouponInterface $imp
     * @param Configuration   $config
     *
     * @return Coupon
     */
    public function getCoupon(CouponInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('coupon');
        $imp    = $imp ?: $this->getGatewayImp('CouponImp');

        $coupon = new Coupon($config, $imp);
        return $coupon;
    }

    /**
     * Create subscription object
     *
     * @param SubscriptionInterface $imp
     * @param Configuration         $config
     *
     * @return Subscription
     */
    public function getSubscription(SubscriptionInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('subscription');
        $imp    = $imp ?: $this->getGatewayImp('SubscriptionImp');

        $subscription = new Subscription($config, $imp);
        return $subscription;
    }

    /**
     * Create Invoice object
     *
     * @param InvoiceInterface $imp
     * @param Configuration    $config
     *
     * @return Invoice
     */
    public function getInvoice(InvoiceInterface $imp = null, Configuration $config = null)
    {
        $config = $config ?: $this['configuration']->toConfig('invoice');
        $imp    = $imp ?: $this->getGatewayImp('InvoiceImp');

        $invoice = new Invoice($config, $imp);
        return $invoice;
    }

    /**
     * Get a new payment primitive optionally initialising the data
     *
     * @param string       $class
     * @param array|object $data
     *
     * @return AbstractPayment
     */
    public function getPaymentType($class = 'card', $data = null)
    {
        switch (strtolower($class)) {
            case 'cc':
            case 'card':
            case 'creditcard':
            case 'credit':
                return new CreditCard($data);
        }

        return null;
    }

    /**
     * Create Notify object
     *
     * @param NotifyInterface $imp
     *
     * @return Notify
     */
    public function getNotify(NotifyInterface $imp = null)
    {
        $imp = $imp ?: $this->getGatewayImp('NotifyImp');

        $notify = new Notify($this, $imp);
        return $notify;
    }

    /**
     * Gets the event manager singleton
     *
     * @return Events
     * @deprecated as of v1.1 use property access instead
     */
    public function getEvents()
    {
        return $this['events'];
    }

    /**
     * Create a gateway implementation
     *
     * @param string $name
     * @param string $namespace
     *
     * @return mixed
     */
    public function getGatewayImp($name, $namespace = null)
    {
        $imp       = null;
        $namespace = $namespace ?: $this['configuration']->get('gateway.namespace');

        if ($namespace) {
            if (strpos($namespace, '\\') !== 0) {
                $namespace = '\\Simplerenew\\Gateway\\' . $namespace;
            }
            $className = $namespace . '\\' . $name;
            if (class_exists($className)) {
                $config = $this['configuration']->toConfig('gateway');
                $imp    = new $className($config);
            }
        }
        return $imp;
    }
}
