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
    const STATUS_ACTIVE  = 1;
    const STATUS_EXPIRED = 0;
    const STATUS_UNKNOWN = -1;

    public $id = null;
    public $plan = null;
    public $status = null;
    public $amount = null;
    public $currency = null;
    public $quantity = null;
    public $enrolled = null;
    public $canceled = null;
    public $expires = null;
    public $period_start = null;
    public $period_end = null;
    public $trial_start = null;
    public $trial_end = null;

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

    public function cancel()
    {
        throw new Exception('Under Construction');
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
