<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

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
     * @var string
     */
    protected $userAdapterClass = null;

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
        $userAdapterClass = empty($config['user']['adapter']) ? null : $config['user']['adapter'];
        if (strpos($userAdapterClass, '\\') === false) {
            $userAdapterClass = '\\Simplerenew\\User\\Adapter\\' . ucfirst(strtolower($userAdapterClass));
        }
        if (!class_exists($userAdapterClass)) {
            throw new Exception('User adapter not found - ' . $userAdapterClass);
        }
        $this->userAdapterClass = $userAdapterClass;

        // Get and verify Gateway configurations
        if (!empty($config['gateway'])) {
            $gateway = $config['gateway'];

            $gatewayNamespace = empty($gateway['name']) ? 'recurly': $gateway['name'];
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
     * @return User\User
     * @throws Exception
     */
    public function getUser()
    {
        $className = $this->userAdapterClass;
        $adapter   = new $className();
        $user      = new User\User($adapter);
        return $user;
    }

    /**
     * Create a new Api\Account object
     *
     * @return Api\Account
     * @throws Exception
     */
    public function getAccount()
    {
        $className = $this->gatewayNamespace . '\\AccountImp';

        $imp     = new $className($this->gatewayConfig);
        $account = new Api\Account($imp, $this->accountConfig);

        return $account;
    }

    public function getBilling()
    {
        $className = $this->gatewayNamespace . '\\BillingImp';

        $imp     = new $className($this->gatewayConfig);
        $billing = new Api\Billing($imp);

        return $billing;
    }
}
