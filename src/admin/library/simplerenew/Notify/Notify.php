<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify;

use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Container;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Logger;
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
    public $handler = null;

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
     * @var Plan
     */
    public $plan = null;

    /**
     * @var NotifyInterface
     */
    protected $adapter = null;

    /**
     * @var Container
     */
    protected $container = null;

    public function __construct(NotifyInterface $adapter, Container $container)
    {
        $this->adapter   = $adapter;
        $this->container = $container;
    }

    /**
     * Process the push notification
     *
     * @param string $package
     */
    public function process($package)
    {
        $this->adapter->loadPackage($this, $package);


        // Convert gateway sourced account data to SR Api Objects
        if ($this->account) {
            $this->account = $this->container
                ->getAccount()
                ->bindSource($this->account);

            $this->account_code = $this->account->code;

            // Load the user for this account
            $userId = $this->account->getUserId();
            if ($userId) {
                try {
                    $this->user = $this->container
                        ->getUser()
                        ->load($userId);

                    $this->user_id = $this->user->id;

                } catch (NotFound $e) {
                    // User must have been deleted from system
                }
            }
        }

        if ($this->billing) {
            $this->billing = $this->container
                ->getBilling()
                ->bindSource($this->billing);
        }

        if ($this->subscription) {
            $this->subscription = $this->container
                ->getSubscription()
                ->bindSource($this->subscription);

            $this->subscription_id = $this->subscription->id;
        }

        $handlerClass = '\\Simplerenew\\Notify\\Handler\\' . ucfirst(strtolower($this->type));
        if (!class_exists($handlerClass)) {
            $this->handler = 'None';
        } else {
            /** @var HandlerInterface $handler */
            $handler = new $handlerClass();
            $handler->execute($this, $this->container);

            $this->handler = get_class($handler);
        }

        Logger::addEntry($this);
    }

    public function getContainer()
    {
        return $this->container;
    }
}
