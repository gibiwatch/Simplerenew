<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
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

    public function __construct(CouponInterface $imp, array $config = array())
    {
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
     * Calculate the discount amount for the selected plan
     *
     * @param Plan $plan
     *
     * @return float
     */
    public function getDiscount(Plan $plan)
    {
        $amount = 0;

        if ($this->isAvailable($plan)) {
            switch ($this->type) {
                case static::TYPE_AMOUNT:
                    $amount = $this->amount;
                    break;

                case static::TYPE_PERCENT:
                    $amount = $plan->amount * ($this->amount / 100);
                    break;
            }
        }

        return $amount;
    }
}
