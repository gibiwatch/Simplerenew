<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception;
use Simplerenew\Exception\Duplicate;
use Simplerenew\Exception\NotFound;
use Simplerenew\Exception\NotSupported;

defined('_JEXEC') or die();

interface SubscriptionInterface
{
    /**
     * Create a new subscription for the selected account in the selected plan
     *
     * @param Subscription $parent
     * @param Account      $account
     * @param Plan         $plan
     * @param Coupon       $coupon
     *
     * @return void
     * @throws Exception
     */
    public function create(Subscription $parent, Account $account, Plan $plan, Coupon $coupon = null);

    /**
     * @param Subscription $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Subscription $parent);

    /**
     * @param Subscription $template
     * @param Account      $account
     * @param int          $bitMask Subscription status codes to retrieve
     *
     * @return array
     */
    public function getList(Subscription $template, Account $account, $bitMask = null);

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
     * Terminate this subscription immediately
     *
     * @param Subscription $parent
     * @param int          $refundType Subscription::REFUND_<type>
     *
     * @return void
     * @throws Exception
     */
    public function terminate(Subscription $parent, $refundType);

    /**
     * Update subscription to a different plan
     *
     * @param Subscription $parent
     * @param Plan         $plan
     * @param Coupon       $coupon
     * @param bool         $immediate
     *
     * @return void
     * @throws Exception
     */
    public function update(Subscription $parent, Plan $plan, Coupon $coupon = null, $immediate = false);

    /**
     * If the current subscription is in trial, extend it by the specified amount
     *
     * @param Subscription  $parent
     * @param \DateInterval $interval
     *
     * @return void
     * @throws Exception
     * @throws NotSupported Subscription is not in trial
     * @throws Duplicate    Trial has already been extended once
     */
    public function extendTrial(Subscription $parent, \DateInterval $interval);

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Subscription $parent
     * @param mixed        $data
     *
     * @return void
     */
    public function bindSource(Subscription $parent, $data);
}
