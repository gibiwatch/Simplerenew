<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Api\Coupon;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface CouponInterface
{
    /**
     * Create a new coupon
     *
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function create(Coupon $parent);

    /**
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Coupon $parent);

    /**
     * @param Coupon  $parent
     * @param Account $account
     *
     * @return Coupon
     */
    public function loadRedeemed(Coupon $parent, Account $account);

    /**
     * Delete coupon
     *
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Coupon $parent);

    /**
     * Get a list of Coupons
     *
     * @param Coupon $template
     * @param int    $status
     *
     * @return array
     * @throws Exception
     */
    public function getList(Coupon $template, $status = null);

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Coupon $parent
     * @param mixed  $data
     *
     * @return void
     */
    public function bindSource(Coupon $parent, $data);

    /**
     * Activate a coupon for a selected account
     *
     * @param Coupon  $parent
     * @param Account $account
     *
     * @return void
     */
    public function activate(Coupon $parent, Account $account);

    /**
     * Deactivate a coupon for the selected account
     *
     * @param Coupon  $parent
     * @param Account $account
     *
     * @return void
     */
    public function deactivate(Coupon $parent, Account $account);
}
