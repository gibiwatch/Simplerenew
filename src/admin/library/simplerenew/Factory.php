<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Api\Account;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Gateway\PlanInterface;
use Simplerenew\User\Adapter\UserInterface;

defined('_JEXEC') or die();

/**
 * Class Factory
 * @package Simplerenew
 *
 * @TODO    : Investigate replacing with a proper DI container
 */
class Factory
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
        if (!empty($config['gateway'])) {
            $gateway = $config['gateway'];

            $gatewayNamespace = empty($gateway['name']) ? 'recurly' : $gateway['name'];
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
     * @return User\User
     * @throws Exception
     */
    public function getUser(UserInterface $adapter = null)
    {
        if (!$adapter) {
            $adapter = $this->userAdapter;
        }
        $user = new User\User($adapter);
        return $user;
    }

    /**
     * Create a new Api\Account object
     *
     * @param Gateway\AccountInterface
     *
     * @return Account
     * @throws Exception
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
     * @return Api\Billing
     */
    public function getBilling(BillingInterface $imp = null)
    {
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\BillingImp';
            $imp       = new $className($this->gatewayConfig);
        }

        $billing = new Api\Billing($imp);
        return $billing;
    }

    /**
     * @param PlanInterface $imp
     *
     * @return Api\Plan
     */
    public function getPlan(PlanInterface $imp = null)
    {
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\PlanImp';
            $imp = new $className($this->gatewayConfig);
        }

        $plan = new Api\Plan($imp);
        return $plan;
    }
}
