<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify;

use Simplerenew\AbstractLogger;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Api\Transaction;
use Simplerenew\Configuration;
use Simplerenew\Container;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Notify\Handler\HandlerInterface;
use Simplerenew\Object;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class Notify extends Object
{
    // Notification types
    const TYPE_ACCOUNT      = 'account';
    const TYPE_BILLING      = 'billing';
    const TYPE_INVOICE      = 'invoice';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_PAYMENT      = 'payment';
    const TYPE_UNKNOWN      = 'unknown';

    // Notification actions
    const ACTION_NEW        = 'new';
    const ACTION_CANCEL     = 'cancel';
    const ACTION_UPDATE     = 'update';
    const ACTION_REACTIVATE = 'reactivate';
    const ACTION_CLOSE      = 'close';
    const ACTION_PAST_DUE   = 'past_due';
    const ACTION_EXPIRE     = 'expire';
    const ACTION_RENEW      = 'renew';
    const ACTION_SUCCESS    = 'success';
    const ACTION_FAIL       = 'fail';
    const ACTION_REFUND     = 'refund';
    const ACTION_VOID       = 'void';
    const ACTION_UNKNOWN    = 'unknown';

    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $action = null;

    /**
     * @var string
     */
    public $package = null;

    /**
     * @var string
     */
    public $user_id = null;

    /**
     * @var string
     */
    public $account_code = null;

    /**
     * @var string
     */
    public $subscription_id = null;

    /**
     * @var User
     */
    public $user = null;

    /**
     * @var Account
     */
    public $account = null;

    /**
     * @var Billing
     */
    public $billing = null;

    /**
     * @var Subscription
     */
    public $subscription = null;

    /**
     * @var Coupon
     */
    public $coupon = null;

    /**
     * @var Invoice
     */
    public $invoice = null;

    /**
     * @var Plan
     */
    public $plan = null;

    /**
     * @var Transaction
     */
    public $transaction = null;

    /**
     * @var NotifyInterface
     */
    protected $adapter = null;

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var array
     */
    protected $allContainers = array();

    public function __construct(Container $container, NotifyInterface $adapter)
    {
        $this->container = $container;
        $this->adapter   = $adapter;
    }

    /**
     * Process the push notification
     *
     * @param string $package
     * @param array  $containers
     */
    public function process($package, array $containers)
    {
        // Verify and save list of gateway containers for possible later use
        foreach ($containers as $container) {
            if ($container instanceof Container) {
                $this->allContainers[$container->gateway] = $container;
            }
        }

        $this->loadFromGatewayData($package);

        $handler  = $this->getHandler($this->type);
        $response = $handler ? $handler->execute($this) : null;
        $this->addLogEntry($handler, $response);

        $this->container->events->trigger('simplerenewNotifyProcess', array($this));
    }

    /**
     * Load data from gateway package and convert to standardized objects
     *
     * @param string $package
     *
     * @return void;
     */
    protected function loadFromGatewayData($package)
    {
        $data = new Configuration($this->adapter->loadPackage($package));

        $this->type    = $data->get('type', static::TYPE_UNKNOWN);
        $this->action  = $data->get('action', static::ACTION_UNKNOWN);
        $this->package = $package;

        // Account and user
        $this->account = $this->container->account->bindSource($data->get('account'));
        $this->user    = $this->container->user;
        if ($this->account->code) {
            if ($userId = $this->account->getUserId()) {
                try {
                    $this->user->load($userId);

                } catch (NotFound $e) {
                    // User must have been deleted from system
                }
            }
        }

        // Load other expected/possible API objects
        $this->billing      = $this->container->billing->bindSource($data->get('billing', array()));
        $this->subscription = $this->container->subscription->bindSource($data->get('subscription', array()));
        $this->coupon       = $this->container->coupon->bindSource($data->get('coupon', array()));
        $this->plan         = $this->container->plan->bindSource($data->get('plan', array()));
        $this->invoice      = $this->container->invoice->bindSource($data->get('invoice', array()));
        $this->transaction  = $this->container->transaction->bindSource($data->get('transaction', array()));

        // Set the properties used for making log entries
        $this->account_code    = $this->account->code;
        $this->user_id         = $this->user->id;
        $this->subscription_id = $this->subscription->id;
    }

    /**
     * Make an entry in the push notification log
     *
     * @param string         $handler
     * @param string         $response
     * @param LogEntry       $entry
     * @param AbstractLogger $logger
     *
     * @return void
     */
    public function addLogEntry($handler, $response, LogEntry $entry = null, AbstractLogger $logger = null)
    {
        $logger = $logger ?: $this->container->logger;
        $entry  = $entry ?: new LogEntry($this);

        $handlerName = $handler ?: 'none';

        $entry->handler  = is_object($handlerName) ? get_class($handlerName) : $handlerName;
        $entry->response = $response ?: 'none';

        $logger->add($entry);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Container[]
     */
    public function getAllContainers()
    {
        return $this->allContainers;
    }

    /**
     * @param string $className
     *
     * @return null|HandlerInterface
     */
    public function getHandler($className)
    {
        if (strpos($className, '\\') === false) {
            $className = '\\Simplerenew\\Notify\\Handler\\' . ucfirst(strtolower($className));
        }

        if (class_exists($className)) {
            return new $className();
        }

        return null;
    }
}
