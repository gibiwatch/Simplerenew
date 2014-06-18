<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiPlan extends RecurlyApibase
{
    protected $classname = 'Recurly_Plan';

    /**
     * @var int
     */
    protected $userGroupId = null;

    /**
     * @var string
     */
    protected $planType = null;

    /**
     * Plans require at least a code and name.
     * @TODO: Would also like to test for valid amount/currency
     *
     * @return bool
     */
    public function isValid()
    {
        if (parent::isValid()) {
            return ($this->plan_code != '')
                && ($this->name != '');
        }
        return false;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        if ($this->isValid()) {
            return $this->recurly->unit_amount_in_cents['USD']->amount_in_cents / 100;
        }
        return 0;
    }

    /**
     * Get Joomla User Group this plan is bound to
     *
     * @return int
     */
    public function getUserGroup()
    {
        if ($this->userGroupId === null) {
            $this->userGroupId = 0;
            if ($this->isValid()) {
                $list = new RecurlyApiPlanList();
                $map  = $list->getMap();
                if (!empty($map[$this->plan_code])) {
                    $this->userGroupId = $map[$this->plan_code];
                } elseif (!empty($map['*'])) {
                    $this->userGroupId = $map['*'];
                }
            }
        }
        return $this->userGroupId;
    }

    /**
     * Get a user friendly string for the plan length
     *
     * @return string
     */
    public function getLength()
    {
        if ($this->isValid()) {
            $unit = 'COM_RECURLY_N_PLAN_' . $this->recurly->plan_interval_unit;
            return
                $this->recurly->plan_interval_length . ' '
                . JText::plural($unit, $this->recurly->plan_interval_length);
        }
        return '';
    }

    public function getPlan_interval_unit()
    {
        if ($this->recurly->plan_interval_unit != '') {
            $unit = 'COM_RECURLY_N_PLAN_' . $this->recurly->plan_interval_unit;
            return JText::plural($unit, $this->recurly->plan_interval_length);
        }
        return '';
    }

    /**
     * Get the short name of the access level for this plan
     *
     * @return string
     */
    public function getType()
    {
        if ($this->planType === null) {
            $list   = new RecurlyApiPlanList();
            $levels = $list->levels;
            foreach ($levels as $level) {
                if (preg_match($level->regexp, $this->name)) {
                    $this->planType = $level->name;
                    break;
                }
            }
        }
        return $this->planType;
    }

    public function getDays()
    {
        $length = 0;
        if ($this->isValid()) {
            $length = $this->recurly->plan_interval_length;
            switch ($this->recurly->plan_interval_unit) {
                case 'months':
                    $length *= 30;
                    break;

                case 'weeks':
                    $length *= 7;
                    break;

                default:
                    // hopefully this doesn't happen
            }
        }
        return $length;
    }

    /**
     * Compare the current plan to the selected plan. Returns an
     * integer indicating if a change to the selected plan represents
     * an upgrade (positive), downgrade (negative) or no change (0)
     * @param $planCode
     *
     * @return int|null|string
     */
    public function compare($planCode)
    {
        $newPlan = new RecurlyApiPlan($planCode);
        if ($this->isValid() && $newPlan->isValid()) {
            $list = new RecurlyApiPlanList();
            $levels = $list->getLevels();
            foreach ($levels as $i => $level) {
                if (!isset($current) && preg_match($level->regexp, $this->name)) $current = $i + 1;
                if (!isset($new) && preg_match($level->regexp, $newPlan->name))  $new = $i + 1;
                if (isset($current) && isset($new)) break;
            }

            if (isset($current) && isset($new)) {
                if ($current != $new) {
                    // Change of access level
                    return $current - $new;
                }

                // No change in access level, calculate based on days
                $change = $newPlan->days - $this->days;
                return $change < 0 ? -1 : ($change > 0 ? 1 : 0);
            }
            return null;
        }
    }
}
