<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiCoupon extends RecurlyApibase
{
    protected $classname = 'Recurly_Coupon';

    public function isValid()
    {
        if (parent::isValid()) {
            return ($this->coupon_code != '')
                && ($this->name != '')
                && (in_array($this->discount_type, array('percent', 'dollars')));
        }
        return false;
    }

    /**
     * Validate the coupon against the selected plan
     *
     * @param string $planCode
     *
     * @return bool
     */
    public function isSupported($planCode = null)
    {
        if (parent::isValid()) {
            if (!in_array($this->recurly->state, array('expired', 'inactive'))) {
                if ($this->recurly->applies_to_all_plans) {
                    return true;
                }

                foreach ((array)$this->recurly->plan_codes as $plan) {
                    if ($planCode == $plan) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get the value of the coupon optionally validated against the selected plan
     * percentage discounts returned as >0, fixed discounts as <0
     *
     * @param string $planCode
     *
     * @return float
     */
    public function getValue($planCode = null)
    {
        $value = 0;
        if ($this->isSupported($planCode)) {
            switch ($this->recurly->discount_type) {
                case 'dollars':
                    $value = -($this->recurly->discount_in_cents['USD']->amount_in_cents / 100);
                    break;

                case 'percent':
                    $value = $this->recurly->discount_percent;
                    break;

                default:
                    $value = 0;
            }
        }

        return $value;
    }

    public function getValueAsString($planCode = null)
    {
        $value = $this->getValue($planCode);
        if ($value == 0) {
            $string = '';
        } elseif ($value < 0) {
            $string = '$' . number_format(abs($value), 2);
        } else {
            $string = number_format($value, 0) . '%';
        }
        return $string;
    }

    /**
     * @param $price
     *
     * @return number
     */
    public function getDiscount($price)
    {
        $value    = $this->getValue();
        $discount = $value > 0 ? $price * ($value / 100) : abs($value);
        return $discount;
    }
}
