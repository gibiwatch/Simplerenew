<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify;

use Simplerenew\AbstractLogger;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Api\Transaction;
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
        $this->adapter->loadPackage($this, $package);

        $this->package         = $package;
        $this->account_code    = $this->account ? $this->account->code : null;
        $this->user_id         = $this->user ? $this->user->id : null;
        $this->subscription_id = $this->subscription ? $this->subscription->id : null;

        $handler  = $this->getHandler($this->type);
        $response = $handler ? $handler->execute($this) : null;
        $this->addLogEntry($handler, $response);

        $this->container->events->trigger('simplerenewNotifyProcess', array($this));
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

    /**
     * Utility function testing for allowable IP addresses
     *
     * @param string       $ip      The target IP to test
     * @param array|string $allowed IP addresses or CIDRs
     *
     * @return bool
     */
    public function IPAllowed($ip, $allowed)
    {
        $allowed = (array)$allowed;
        $ipLong  = ip2long($ip);

        foreach ($allowed as $range) {
            if (strpos($range, '/')) {
                // CIDR test
                list ($subNet, $bits) = explode('/', $range);
                $subNet = ip2long($subNet);
                $mask   = -1 << (32 - $bits);

                $subNet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
                if (($ipLong & $mask) == $subNet) {
                    return true;
                }
            } else {
                // Single IP
                if ($ip == $range) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Update the user's groups based on subscribed plans in all gateways
     *
     * @return void
     */
    public function updateUserGroups()
    {
        if ($this->user) {
            $plans = array();
            foreach ($this->allContainers as $container) {
                try {
                    $account       = $container->account->load($this->user);
                    $subscriptions = $container
                        ->subscription
                        ->getList($account, !Subscription::STATUS_EXPIRED);

                    foreach ($subscriptions as $subscription) {
                        $plans[] = $subscription->plan;
                    }

                } catch (NotFound $e) {
                    // Perfectly fine
                }
            }
            $this->user->addGroups($plans, true);
        }
    }
}
