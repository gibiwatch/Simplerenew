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
 * @TODO    : Investigate replacing with a proper DI container
 *
 * @property Configuration   $configuration
 * @property Account         $account
 * @property Billing         $billing
 * @property Coupon          $coupon
 * @property Invoice         $invoice
 * @property Notify          $notify
 * @property AbstractPayment $paymenttype
 * @property Plan            $plan
 * @property Subscription    $subscription
 */
class Container
{
    /**
     * @var Configuration
     */
    protected $configuration = null;

    public function __construct(array $config)
    {
        $this->configuration = new Configuration(array());
        $config              = new Configuration($config);

        // Load account settings
        $account = $config->get('account', array());
        $this->configuration->set('account.config', $account);

        // Load and verify user settings
        $userAdapter = $config->get('user.adapter');
        if (is_string($userAdapter)) {
            if (strpos($userAdapter, '\\') === false) {
                $userAdapter = '\\Simplerenew\\User\\Adapter\\' . ucfirst(strtolower($userAdapter));
            }
            if (class_exists($userAdapter)) {
                $userAdapter = new $userAdapter();
            }
        }
        if ($userAdapter instanceof UserInterface) {
            $this->configuration->set('user.adapter', $userAdapter);
            $this->configuration->set('user.config', $config->get('user', array()));
        }

        // Load and verify Gateway configurations
        $gateway = $config->get('gateway', array());
        if (!empty($gateway)) {
            $namespace = key($gateway);
            if (!empty($gateway[$namespace])) {
                $gatewayConfig = new Configuration($gateway[$namespace]);
                $this->configuration->set('gateway.config', $gatewayConfig);
            }

            if (strpos($namespace, '\\') === false) {
                $namespace = '\\Simplerenew\\Gateway\\' . ucfirst(strtolower($namespace));
            }
            $this->configuration->set('gateway.namespace', $namespace);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'configuration':
                return $this->configuration;
                break;

            default:
                $method = 'get' . ucfirst(strtolower($name));
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
        $adapter = $adapter ?: $this->configuration->get('user.adapter');
        $user    = new User($this, $adapter);
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
        $imp     = $imp ?: $this->createGatewayInstance('AccountImp');
        $account = new Account($this, $imp);
        return $account;
    }

    /**
     * @param BillingInterface $imp
     *
     * @return Billing
     */
    public function getBilling(BillingInterface $imp = null)
    {
        $imp     = $imp ?: $this->createGatewayInstance('BillingImp');
        $billing = new Billing($this, $imp);
        return $billing;
    }

    /**
     * @param PlanInterface $imp
     *
     * @return Plan
     */
    public function getPlan(PlanInterface $imp = null)
    {
        $imp  = $imp ?: $this->createGatewayInstance('PlanImp');
        $plan = new Plan($this, $imp);
        return $plan;
    }

    /**
     * @param CouponInterface $imp
     *
     * @return Coupon
     */
    public function getCoupon(CouponInterface $imp = null)
    {
        $imp    = $imp ?: $this->createGatewayInstance('CouponImp');
        $coupon = new Coupon($this, $imp);
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
        $imp          = $imp ?: $this->createGatewayInstance('SubscriptionImp');
        $subscription = new Subscription($this, $imp);
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
        $imp     = $imp ?: $this->createGatewayInstance('InvoiceImp');
        $invoice = new Invoice($this, $imp);
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
        $imp = $imp ?: $this->createGatewayInstance('NotifyImp');

        $notify = new Notify($this, $imp);
        return $notify;
    }

    /**
     * Create a new Gateway object using the configured gateway namespace
     *
     * @param $name
     *
     * @return mixed
     * @throws Exception
     */
    public function createGatewayInstance($name)
    {
        $namespace = $this->configuration->get('gateway.namespace');
        $className = $namespace . '\\' . $name;
        if (class_exists($className)) {
            $config   = $this->configuration->get('gateway.config');
            $instance = new $className($config);

            return $instance;
        }

        throw new Exception('Unknown Gateway Object - ' . $className);
    }
}
