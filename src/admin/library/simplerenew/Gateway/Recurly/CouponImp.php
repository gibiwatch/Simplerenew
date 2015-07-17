<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Coupon;
use Simplerenew\Exception;
use Simplerenew\Exception\Duplicate;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\CouponInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class CouponImp extends AbstractRecurlyBase implements CouponInterface
{
    const CODE_ILLEGAL    = '/([^a-zA-Z0-9@\-_\.])/';
    const CODE_MAX_LENGTH = 50;

    protected $fieldMap = array(
        'code'              => 'coupon_code',
        'short_description' => 'invoice_description',
        'description'       => 'hosted_description',
        'status'            => array(
            'state' => array(
                'redeemable'          => Coupon::STATUS_ACTIVE,
                'expired'             => Coupon::STATUS_EXPIRED,
                'maxed_out'           => Coupon::STATUS_MAX,
                'inactive'            => Coupon::STATUS_EXPIRED,
                Object::MAP_UNDEFINED => Coupon::STATUS_UNKNOWN
            )
        ),
        'type'              => array(
            'discount_type' => array(
                'percent'             => Coupon::TYPE_PERCENT,
                'dollars'             => Coupon::TYPE_AMOUNT,
                Object::MAP_UNDEFINED => null
            )
        ),
        'expires'           => 'redeem_by_date',
        'max_uses'          => 'max_redemptions',
        'plans'             => 'plan_codes',
        'created'           => 'created_at'
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
        $required = array(
            'Code'   => $parent->code,
            'Name'   => $parent->name,
            'Type'   => $parent->type,
            'Amount' => $parent->amount
        );
        if ($missing = array_diff($required, array_filter($required))) {
            throw new Exception('Required coupon data missing: ' . join(', ', array_keys($missing)));
        }
        if (preg_match_all(static::CODE_ILLEGAL, $parent->code, $illegal)) {
            throw new Exception('Invalid characters \'' . join('', $illegal[1]) . '\' in coupon code');
        }
        if (strlen($parent->code) > static::CODE_MAX_LENGTH) {
            throw new Exception('Coupon code must be no longer than ' . static::CODE_MAX_LENGTH . ' characters');
        }

        \Recurly_Client::$apiKey = $this->client->apiKey();
        $coupon                  = new \Recurly_Coupon();

        $data = $this->reverseMap($parent->getProperties(), $this->fieldMap);
        foreach ($data as $field => $value) {
            $coupon->$field = $value;
        }
        $coupon->applies_to_all_plans = (bool)!$parent->plans;

        if ($parent->type == Coupon::TYPE_AMOUNT) {
            if (!$parent->currency) {
                throw new Exception('Currency must be specified for fixed amount coupons');
            }
            $coupon->discount_in_cents->addCurrency($parent->currency, $parent->amount * 100);
        } else {
            $coupon->discount_percent = $parent->amount;
        }

        try {
            $coupon->create();
            $this->bindSource($parent, $coupon);

        } catch (\Recurly_ValidationError $e) {
            $message = array();

            foreach ($e->errors as $fieldError) {
                if ($fieldError->field == 'coupon_code' && $fieldError->symbol == 'taken') {
                    $message = sprintf("'%s' %s", $coupon->coupon_code, $fieldError->description);
                    throw new Duplicate($message, 0, $e);
                }
                $message[] = sprintf("'%s' %s", $fieldError->field, $fieldError->description);
            }
            throw new Exception(join("\n", $message), 0, $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }
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
     * @param mixed  $data
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
                    $parent->amount = $this->getCurrency($parent->amount);
                }
                $parent->currency = $this->currency;

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
        $coupon = $this->getCoupon($parent->code);
        $coupon->delete();
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
        $coupons = array();
        $states  = $this->translateStatus($status);

        $rawCoupons = \Recurly_CouponList::get(null, $this->client);
        foreach ($rawCoupons as $rawCoupon) {
            if (in_array($rawCoupon->state, $states)) {
                $coupon = clone $template;
                $this->bindSource($coupon, $rawCoupon);
                $coupons[$coupon->code] = $coupon;
            }
        }
        return $coupons;
    }

    /**
     * Translate a SR status into Recurly states. There can be multiple
     * states for a single status.
     *
     * @param int $status
     *
     * @return array
     */
    protected function translateStatus($status)
    {
        $states = array();
        foreach ($this->fieldMap['status']['state'] as $state => $value) {
            if ($status == $value) {
                $states[] = $state;
            }
        }
        return $states;
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
