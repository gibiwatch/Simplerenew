<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Events\Events;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Gateway\CouponInterface;
use Simplerenew\Gateway\InvoiceInterface;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Gateway\PlanInterface;
use Simplerenew\Gateway\SubscriptionInterface;
use Simplerenew\Notify\Notify;
use Simplerenew\Primitive\CreditCard;
use Simplerenew\Primitive\AbstractPayment;
use Simplerenew\User\Adapter\UserInterface;
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
 * @property Events          $events
 * @property Invoice         $invoice
 * @property Notify          $notify
 * @property Plan            $plan
 * @property Subscription    $subscription
 */
class Container
{
    /**
     * @var Configuration
     */
    protected $configuration = null;

    /**
     * @var Events
     */
    protected $events = null;

    public function __construct(Configuration $config)
    {
        $this->configuration = $config;
    }

    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'configuration':
                return $this->configuration;
                break;

            case 'gatewayimp':
                // Hide the gatewayImp getter
                break;

            default:
                $method = 'get' . ucfirst($name);
                if (method_exists($this, $method)) {
                    return $this->$method();
                }
                break;
        }

        return null;
    }

    /**
     * Create a new user object
     *
     * @param UserInterface $adapter
     *
     * @return User
     * @throws Exception
     */
    public function getUser(UserInterface $adapter = null)
    {
        $config = $this->configuration->getConfig('user');
        if (!$adapter) {
            $className = '\\Simplerenew\\User\\Adapter\\' . $config->get('adapter');
            $adapter   = new $className();
        }

        $user = new User($config, $adapter);
        return $user;
    }

    /**
     * Create a new Account object
     *
     * @param AccountInterface $imp
     *
     * @return Account
     */
    public function getAccount(AccountInterface $imp = null)
    {
        $config = $this->configuration->getConfig('account');
        $imp    = $imp ?: $this->getGatewayImp('AccountImp');

        $account = new Account($config, $imp);
        return $account;
    }

    /**
     * @param BillingInterface $imp
     *
     * @return Billing
     */
    public function getBilling(BillingInterface $imp = null)
    {
        $config = $this->configuration->getConfig('billing');
        $imp    = $imp ?: $this->getGatewayImp('BillingImp');

        $billing = new Billing($config, $imp);
        return $billing;
    }

    /**
     * @param PlanInterface $imp
     *
     * @return Plan
     */
    public function getPlan(PlanInterface $imp = null)
    {
        $config = $this->configuration->getConfig('plan');
        $imp    = $imp ?: $this->getGatewayImp('PlanImp');

        $plan = new Plan($config, $imp);
        return $plan;
    }

    /**
     * @param CouponInterface $imp
     *
     * @return Coupon
     */
    public function getCoupon(CouponInterface $imp = null)
    {
        $config = $this->configuration->getConfig('coupon');
        $imp    = $imp ?: $this->getGatewayImp('CouponImp');

        $coupon = new Coupon($config, $imp);
        return $coupon;
    }

    /**
     * Create subscription object
     *
     * @param SubscriptionInterface $imp
     *
     * @return Subscription
     */
    public function getSubscription(SubscriptionInterface $imp = null)
    {
        $config = $this->configuration->getConfig('subscription');
        $imp    = $imp ?: $this->getGatewayImp('SubscriptionImp');

        $subscription = new Subscription($config, $imp);
        return $subscription;
    }

    /**
     * Create Invoice object
     *
     * @param InvoiceInterface $imp
     *
     * @return Invoice
     */
    public function getInvoice(InvoiceInterface $imp = null)
    {
        $config = $this->configuration->getConfig('invoice');
        $imp    = $imp ?: $this->getGatewayImp('InvoiceImp');

        $invoice = new Invoice($config, $imp);
        return $invoice;
    }

    /**
     * Get a new payment primitive optionally initialising the data
     *
     * @param string $class
     * @param array  $data
     *
     * @return AbstractPayment
     */
    public function getPaymentType($class = 'card', array $data = null)
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
     * @param array $events
     *
     * @return Events
     */
    public function getEvents(array $events = array())
    {
        if ($this->events === null) {
            $events       = $events ?: $this->configuration->get('events', array());
            $this->events = new Events($events);

        } elseif ($events) {
            $this->events->registerEvents($events);
        }

        return $this->events;
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
        $namespace = $namespace ?: $this->configuration->get('gateway.namespace');

        if ($namespace) {
            if (strpos($namespace, '\\') !== 0) {
                $namespace = '\\Simplerenew\\Gateway\\' . $namespace;
            }
            $className = $namespace . '\\' . $name;
            if (class_exists($className)) {
                $config = $this->configuration->getConfig('gateway');
                $imp    = new $className($config);
            }
        }
        return $imp;
    }
}
