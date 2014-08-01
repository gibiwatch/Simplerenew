<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify;

use Simplerenew\Api\AbstractApiBase;
use Simplerenew\Container;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Notify\Handler\HandlerInterface;
use Simplerenew\Object;

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
     * @var object
     */
    public $user = null;

    /**
     * @var object
     */
    public $account = null;

    /**
     * @var object
     */
    public $billing = null;

    /**
     * @var object
     */
    public $subscription = null;

    /**
     * @var NotifyInterface
     */
    protected $imp = null;

    /**
     * @var Container
     */
    protected $container = null;

    public function __construct(NotifyInterface $imp, Container $container)
    {
        $this->imp       = $imp;
        $this->container = $container;
    }

    /**
     * Process the push notification
     *
     * @param string    $package
     */
    public function process($package)
    {
        $this->imp->loadPackage($this, $package);

        // Gateway sourced data to SR fields
        if ($this->account) {
            $account = $this->container->getAccount();
            $account->bindSource($this->account);
            $this->account = $account->getProperties();
            $this->account_code = $account->code;
        }

        if ($this->billing) {
            $billing = $this->container->getBilling();
            $billing->bindSource($this->billing);
            $this->billing = $billing->getProperties();
        }

        if ($this->subscription) {
            $subscription = $this->container->getSubscription();
            $subscription->bindSource($this->subscription);
            $this->subscription = $subscription->getProperties();
            $this->subscription_id = $subscription->id;
        }


        $classBase = '\\Simplerenew\\Notify\\Handler\\';
        $this->handler = ucfirst(strtolower($this->type));
        if (!class_exists($classBase . $this->handler)) {
            $this->handler = 'None';
        }
        $handlerClass = $classBase . $this->handler;

        /** @var HandlerInterface $handler */
        $handler = new $handlerClass();
        $handler->execute($this, $this->container);
    }
}
