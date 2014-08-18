<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Coupon;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\CouponInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class CouponImp extends AbstractRecurlyBase implements CouponInterface
{
    protected $fieldMap = array(
        'code'     => 'coupon_code',
        'status'   => array(
            'state' => array(
                'redeemable'          => Coupon::STATUS_ACTIVE,
                'expired'             => Coupon::STATUS_EXPIRED,
                'maxed_out'           => Coupon::STATUS_MAX,
                'inactive'            => Coupon::STATUS_EXPIRED,
                Object::MAP_UNDEFINED => Coupon::STATUS_UNKNOWN
            )
        ),
        'type'     => array(
            'discount_type' => array(
                'percent'             => Coupon::TYPE_PERCENT,
                'dollars'             => Coupon::TYPE_AMOUNT,
                Object::MAP_UNDEFINED => null
            )
        ),
        'expires'  => 'redeem_by_date',
        'max_uses' => 'max_redemptions',
        'plans'    => 'plan_codes',
        'created'  => 'created_at'
    );

    /**
     * Create a new coupon
     *
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function create(Coupon $parent)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Coupon $parent)
    {
        $coupon = $this->getCoupon($parent->code);
        $this->bindSource($parent, $coupon);
    }

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Coupon $parent
     * @param mixed   $data
     *
     * @return void
     */
    public function bindSource(Coupon $parent, $data)
    {
        $parent
            ->clearProperties()
            ->setProperties($data, $this->fieldMap);
        $parent->currency = $this->currency;

        $allPlans = $this->getKeyValue($data, 'applies_to_all_plans');
        if ($allPlans) {
            $parent->plans = array();
        }

        $type = $this->getKeyValue($parent, 'type');
        switch ($type) {
            case Coupon::TYPE_AMOUNT:
                $parent->amount = $this->getKeyValue($data, 'discount_in_cents');
                if ($parent->amount instanceof \Recurly_CurrencyList) {
                    $parent->amount   = $this->getCurrency($parent->amount);
                }
                break;

            case Coupon::TYPE_PERCENT:
                $parent->amount = $this->getKeyValue($data, 'discount_percent');
                break;
        }
    }

    /**
     * @param Coupon  $parent
     * @param Account $account
     *
     * @return Coupon
     */
    public function loadRedeemed(Coupon $parent, Account $account)
    {
        // TODO: Implement loadRedeemed() method.
    }

    /**
     * Delete coupon
     *
     * @param Coupon $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Coupon $parent)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Get a list of Coupons
     *
     * @param Coupon $template
     * @param int    $status
     *
     * @return array
     * @throws Exception
     */
    public function getList(Coupon $template, $status = null)
    {
        // TODO: Implement getList() method.
    }

    /**
     * @param string $code
     *
     * @return \Recurly_Coupon
     * @throws Exception
     */
    protected function getCoupon($code)
    {
        if (!$code) {
            throw new Exception('No coupon selected');
        }

        try {
            $coupon = \Recurly_Coupon::get($code, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $coupon;
    }
}
