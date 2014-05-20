<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

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
     * @var User\Adapter\UserInterface
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
     * @param User\Adapter\UserInterface
     *
     * @return User\User
     * @throws Exception
     */
    public function getUser(User\Adapter\UserInterface $adapter = null)
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
     * @return Api\Account
     * @throws Exception
     */
    public function getAccount(Gateway\AccountInterface $imp = null)
    {
        if (!$imp) {
            $className = $this->gatewayNamespace . '\\AccountImp';
            $imp       = new $className($this->gatewayConfig);
        }

        $account = new Api\Account($imp, $this->accountConfig);
        return $account;
    }

    /**
     * @param Gateway\BillingInterface $imp
     *
     * @return Api\Billing
     */
    public function getBilling(Gateway\BillingInterface $imp = null)
    {

        if (!$imp) {
            $className = $this->gatewayNamespace . '\\BillingImp';
            $imp       = new $className($this->gatewayConfig);
        }

        $billing = new Api\Billing($imp);
        return $billing;
    }
}
