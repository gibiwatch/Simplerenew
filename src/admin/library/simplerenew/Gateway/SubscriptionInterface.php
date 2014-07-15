<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface SubscriptionInterface
{
    /**
     * Create a new subscription for the selected account in the selected plan
     *
     * @param Subscription $parent
     * @param Account      $account
     * @param Plan         $plan
     *
     * @return void
     * @throws Exception
     */
    public function create(Subscription $parent, Account $account, Plan $plan);

    /**
     * @param Subscription $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Subscription $parent);

    /**
     * @param Subscription $parent
     * @param Account      $account
     * @param int          $status One of the Simplerenew\Api\Subscription status codes
     *
     * @return array
     */
    public function getList(Subscription $parent, Account $account, $status = null);

    /**
     * Get the most recent subscription
     *
     * @param Subscription $parent
     * @param Account      $account
     *
     * @return void
     * @throws Exception
     */
    public function loadLast(Subscription $parent, Account $account);

    /**
     * Cancel autorenew for this subscription
     *
     * @param Subscription $parent
     *
     * @return void
     * @throws Exception
     */
    public function cancel(Subscription $parent);

    /**
     * Turn autorenew on for this subscription
     *
     * @param Subscription $parent
     *
     * @return void
     * @throws Exception
     */
    public function reactivate(Subscription $parent);

    /**
     * Update subscription to a different plan
     *
     * @param Subscription $parent
     * @param Plan         $plan
     *
     * @return void
     * @throws Exception
     */
    public function update(Subscription $parent, Plan $plan);
}
