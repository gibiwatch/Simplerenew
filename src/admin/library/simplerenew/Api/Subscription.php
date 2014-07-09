<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Api\Account;
use Simplerenew\Api\Plan;
use Simplerenew\Exception;
use Simplerenew\Gateway\SubscriptionInterface;

defined('_JEXEC') or die();

class Subscription extends AbstractApiBase
{
    const STATUS_ACTIVE   = 1;
    const STATUS_CANCELED = 2;
    const STATUS_EXPIRED  = 3;
    const STATUS_UNKNOWN  = 0;

    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $plan = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var float
     */
    public $amount = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var int
     */
    public $quantity = null;

    /**
     * @var \DateTime
     */
    public $enrolled = null;

    /**
     * @var \DateTime
     */
    public $canceled = null;

    /**
     * @var \DateTime
     */
    public $expires = null;

    /**
     * @var \DateTime
     */
    public $period_start = null;

    /**
     * @var \DateTime
     */
    public $period_end = null;

    /**
     * @var \DateTime
     */
    public $trial_start = null;

    /**
     * @var \DateTime
     */
    public $trial_end = null;

    /**
     * @var string
     */
    public $pending_plan = null;

    /**
     * @var float
     */
    public $pending_amount = null;

    /**
     * @var SubscriptionInterface
     */
    protected $imp = null;

    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @param SubscriptionInterface $imp
     * @param array                 $config
     */
    public function __construct(SubscriptionInterface $imp, array $config = array())
    {
        $this->imp = $imp;
    }

    /**
     * Load a specific subscription by system id
     *
     * @param string $id
     *
     * @return Subscription
     * @throws Exception
     */
    public function load($id)
    {
        $this->clearProperties();

        $this->id = $id;
        $this->imp->load($this);

        return $this;
    }

    /**
     * Get list of subscriptions for the selected account
     *
     * @param Account $account
     * @param int     $status One of the Simplerenew\Api\Subscription status codes
     *
     * @return array()
     */
    public function getList(Account $account, $status = null)
    {
        return $this->imp->getList($this, $account, $status);
    }

    public function loadActive(Account $account)
    {
        $this->imp->loadActive($this, $account);
        return $this;
    }

    /**
     * Create a new subscription for the account and plan
     *
     * @param Account $account
     * @param Plan    $plan
     *
     * @return Subscription
     * @throws Exception
     */
    public function create(Account $account, Plan $plan)
    {
        $this->clearProperties();
        $this->account = $account;

        $this->imp->create($this, $account, $plan);
        $account->user->setGroup($plan);

        return $this;
    }

    /**
     * Cancel this subscription
     *
     * @return void
     * @throws Exception
     */
    public function cancel()
    {
        $this->imp->cancel($this);
    }

    public function reactivate()
    {
        throw new Exception('Under Construction');
    }

    public function history(Account $account)
    {
        throw new Exception('Under Construction');
    }
}
