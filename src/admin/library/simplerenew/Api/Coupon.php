<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Exception\InvalidArgument;
use Simplerenew\Gateway\CouponInterface;

defined('_JEXEC') or die();

class Coupon extends AbstractApiBase
{
    // percent, dollars
    const TYPE_PERCENT = 1;
    const TYPE_AMOUNT  = 2;

    // Status bit masks
    const STATUS_ACTIVE  = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_MAX     = 4;
    const STATUS_UNKNOWN = 0;

    /**
     * @var string
     */
    public $code = null;

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $description = null;

    /**
     * @var string
     */
    public $short_description = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var float
     */
    public $amount = null;

    /**
     * @var \DateTime
     */
    public $expires = null;

    /**
     * @var int
     */
    public $max_uses = null;

    /**
     * @var array
     */
    public $plans = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var CouponInterface
     */
    protected $imp = null;

    public function __construct(Configuration $config, CouponInterface $imp)
    {
        parent::__construct($config);

        $this->imp = $imp;
    }

    /**
     * @param $code
     *
     * @return Coupon
     * @throws Exception
     */
    public function load($code)
    {
        $this->clearProperties();
        $this->code = $code;
        $this->imp->load($this);

        return $this;
    }

    /**
     * Validate the coupon against the selected plan
     *
     * @param Plan $plan
     *
     * @return bool
     */
    public function isAvailable(Plan $plan)
    {
        return ($this->status == static::STATUS_ACTIVE
            && (!$this->plans || in_array($plan->code, $this->plans))
        );
    }

    /**
     * Calculate the total discount amount for the selected plans
     *
     * @param Plan|Plan[] $plans
     *
     * @return float
     * @throws InvalidArgument
     */
    public function getDiscount($plans)
    {
        $amount = 0;

        if ($plans instanceof Plan) {
            $plans = array($plans);
        }
        if (!is_array($plans)) {
            throw new InvalidArgument(get_class($this) . '::' . __METHOD__ . ' - expecting array or Plan');
        }

        foreach ($plans as $plan) {
            if ($plan instanceof Plan && $this->isAvailable($plan)) {
                switch ($this->type) {
                    case static::TYPE_AMOUNT:
                        $amount += $this->amount;
                        break;

                    case static::TYPE_PERCENT:
                        $amount += ($plan->amount * ($this->amount / 100));
                        break;
                }
            }
        }

        return $amount;
    }

    /**
     * Create a new coupon on the gateway using current property values
     */
    public function create()
    {
        $this->imp->create($this);
    }

    /**
     * Get list of coupons from the Gateway
     *
     * @param int $status
     *
     * @return array Associative array of coupons keyed on coupon code
     * @throws Exception
     */
    public function getList($status = self::STATUS_ACTIVE)
    {
        $template = clone $this;
        $template->clearProperties();

        $coupons = $this->imp->getList($template, $status);
        ksort($coupons);
        return $coupons;
    }

    /**
     * Remove the coupon from usage
     *
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        $this->imp->delete($this);
    }

    /**
     * Activate this coupon for the selected account
     *
     * @param Account $account
     */
    public function activate(Account $account)
    {
        $this->imp->activate($this, $account);
    }

    /**
     * Deactivate this coupon for the selected account
     *
     * @param Account $account
     */
    public function deactivate(Account $account)
    {
        $this->imp->deactivate($this, $account);
    }

    /**
     * Return a string representation of the coupon amount
     *
     * @return string
     */
    public function amountAsString()
    {
        switch ($this->type) {
            case static::TYPE_AMOUNT:
                return $this->currency . number_format($this->amount, 2);

            case static::TYPE_PERCENT:
                return number_format($this->amount) . '%';
        }

        return '';
    }
}
