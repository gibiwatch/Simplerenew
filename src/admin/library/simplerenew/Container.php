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
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Gateway\PlanInterface;
use Simplerenew\Gateway\SubscriptionInterface;
use Simplerenew\Primitive\CreditCard;
use Simplerenew\Primitive\AbstractPayment;
use Simplerenew\User\Adapter\UserInterface;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Container
 * @package Simplerenew
 *
 * @TODO    : Investigate replacing with a proper DI container
 */
class Container
{
    /**
     * @var UserInterface
     */
    protected $userAdapter = null;

    /**
     * @var string
     */
    protected $gatewayNamespace = null;

    /**
     * @var array
     */
    protected $gatewayConfig = null;

    /**
     * @var array
     */
    protected $accountConfig = null;

    public function __construct(array $config)
    {
        if (!empty($config['account'])) {
            $this->accountConfig = $config['account'];
        }

        // Verify valid user adapter
        $userAdapter = empty($config['user']['adapter']) ? null : $config['user']['adapter'];
        if (is_string($userAdapter)) {
            if (strpos($userAdapter, '\\') === false) {
                $userAdapter = '\\Simplerenew\\User\\Adapter\\' . ucfirst(strtolower($userAdapter));
            }
            if (class_exists($userAdapter)) {
                $userAdapter = new $userAdapter();
            }
        }
        if (!$userAdapter instanceof UserInterface) {
            throw new Exception('User adapter not found - ' . $userAdapter);
        }
        $this->userAdapter = $userAdapter;

        // Get and verify Gateway configurations
        if (empty($config['gateway'])) {
            throw new Exception('No gateway has been defined');
        } else {
            $gateway = $config['gateway'];

            $gatewayNamespace = key($gateway);
            $gateway = $gateway[$gatewayNamespace];

            if (strpos($gatewayNamespace, '\\') === false) {
                $gatewayNamespace = '\\Simplerenew\\Gateway\\' . ucfirst(strtolower($gatewayNamespace));
            }
            if (!class_exists($gatewayNamespace . '\\AccountImp')) {
                throw new Exception('Gateway namespace not valid - ' . $gatewayNamespace);
            }
            $this->gatewayNamespace = $gatewayNamespace;

            $this->gatewayConfig = $gateway;
        }
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
        if (!$adapter) {
            $adapter = $this->userAdapter;
        }
        $user = new User($adapter);
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
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\AccountImp';
            $imp       = new $className($this->gatewayConfig);
        }

        $account = new Account($imp, $this->accountConfig);
        return $account;
    }

    /**
     * @param BillingInterface $imp
     *
     * @return Billing
     */
    public function getBilling(BillingInterface $imp = null)
    {
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\BillingImp';
            $imp       = new $className($this->gatewayConfig);
        }

        $billing = new Billing($imp);
        return $billing;
    }

    /**
     * @param PlanInterface $imp
     *
     * @return Plan
     */
    public function getPlan(PlanInterface $imp = null)
    {
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\PlanImp';
            $imp = new $className($this->gatewayConfig);
        }

        $plan = new Plan($imp);
        return $plan;
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
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\SubscriptionImp';
            $imp = new $className($this->gatewayConfig);
        }

        $subscription = new Subscription($imp);
        return $subscription;
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
}
