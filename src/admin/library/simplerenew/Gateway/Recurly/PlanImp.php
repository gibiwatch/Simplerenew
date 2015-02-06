<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Plan;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\PlanInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class PlanImp extends AbstractRecurlyBase implements PlanInterface
{
    protected $fieldMap = array(
        'code'         => 'plan_code',
        'length'       => 'plan_interval_length',
        'unit'         => array(
            'plan_interval_unit' => array(
                'months'              => Plan::INTERVAL_MONTHS,
                'days'                => Plan::INTERVAL_DAYS,
                Object::MAP_UNDEFINED => Plan::INTERVAL_UNKNOWN
            )
        ),
        'trial_length' => 'trial_interval_length',
        'trial_unit'   => array(
            'trial_interval_unit' => array(
                'months'              => Plan::INTERVAL_MONTHS,
                'days'                => Plan::INTERVAL_DAYS,
                Object::MAP_UNDEFINED => Plan::INTERVAL_UNKNOWN
            )
        ),
        'created'      => 'created_at'
    );

    protected $plansLoaded = array();

    /**
     * Retrieve all subscription plan information
     *
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Plan $parent)
    {
        $plan = $this->getPlan($parent->code);
        $this->bindSource($parent, $plan);
    }

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Plan  $parent
     * @param mixed $data
     *
     * @return void
     */
    public function bindSource(Plan $parent, $data)
    {
        $parent->setProperties($data, $this->fieldMap);

        $amount = $this->getKeyValue($data, 'unit_amount_in_cents');
        if ($amount instanceof \Recurly_CurrencyList) {
            $amount = $this->getCurrency($amount);
        }

        $setup = $this->getKeyValue($data, 'setup_fee_in_cents');
        if ($setup instanceof \Recurly_CurrencyList) {
            $setup = $this->getCurrency($setup);
        }

        $parent->setProperties(
            array(
                'currency'   => $this->currency,
                'amount'     => $amount,
                'setup_cost' => $setup
            )
        );
    }

    /**
     * Get the list of all plans on the gateway
     *
     * @param Plan $template
     *
     * @return array
     * @throws Exception
     */
    public function getList(Plan $template)
    {
        $this->plansLoaded = array();
        try {
            $rawObjects = \Recurly_PlanList::get(null, $this->client);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($rawObjects as $plan) {
            $nextPlan = clone $template;
            $this->bindSource($nextPlan, $plan);
            $this->plansLoaded[$plan->plan_code] = $nextPlan;
        }

        return $this->plansLoaded;
    }

    /**
     * @param string $code
     *
     * @return \Recurly_Plan
     * @throws Exception
     */
    protected function getPlan($code)
    {
        if (!$code) {
            throw new Exception('No plan code selected');
        }

        if (!isset($this->plansLoaded[$code])) {
            try {
                $plan = \Recurly_Plan::get($code, $this->client);

            } catch (\Recurly_NotFoundError $e) {
                throw new NotFound($e->getMessage(), $e->getCode(), $e);

            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }

            $this->plansLoaded[$plan->plan_code] = $plan;
        }
        return $this->plansLoaded[$code];
    }

    /**
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function save(Plan $parent)
    {
        try {
            $plan  = $this->getPlan($parent->code);
            $isNew = false;
        } catch (NotFound $e) {
            $plan            = new \Recurly_Plan(null, $this->client);
            $plan->plan_code = $parent->code;
            $isNew           = true;
        }

        $plan->name            = $parent->name;
        $plan->description     = $parent->description;

        $this->convertLength($parent->length, $parent->unit);
        $plan->plan_interval_length = $parent->length;
        $plan->plan_interval_unit   = $parent->unit;

        if ($parent->trial_length) {
            $this->convertLength($parent->trial_length, $parent->trial_unit);
            $plan->trial_interval_length = $parent->trial_length;
            $plan->trial_interval_unit   = $parent->trial_unit;
        }

        $amount     = $parent->amount * 100;
        $setup_cost = $parent->setup_cost * 100;
        if ($isNew) {
            $plan->unit_amount_in_cents->addCurrency($parent->currency, $amount);
            $plan->setup_fee_in_cents->addCurrency($parent->currency, $setup_cost);

            $plan->create();
            if (
                !$plan->unit_amount_in_cents instanceof \Recurly_CurrencyList
                || !$plan->unit_amount_in_cents->offsetExists($parent->currency)
            ) {
                // there was a problem with the selected currency
                $plan->delete();
                throw new Exception(sprintf('"%s" is not an accepted currency at Recurly', $parent->currency));
            }

        } else {
            if (!$plan->unit_amount_in_cents instanceof \Recurly_CurrencyList) {
                $plan->unit_amount_in_cents = new \Recurly_CurrencyList('unit_amount_in_cents');
            }
            $plan->unit_amount_in_cents[$parent->currency] = $amount;

            if (!$plan->setup_fee_in_cents instanceof \Recurly_CurrencyList) {
                $plan->setup_fee_in_cents = new \Recurly_CurrencyList('setup_fee_in_cents');
            }
            $plan->setup_fee_in_cents[$parent->currency]   = $setup_cost;

            $plan->update();
        }
        $this->bindSource($parent, $plan);
    }

    /**
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Plan $parent)
    {
        try {
            $plan = $this->getPlan($parent->code);

            try {
                $plan->delete();

            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }

        } catch (NotFound $e) {
            // No worries, nothing to delete
        }
    }

    /**
     * Handle interval units not supported by Recurly. Changes
     * parameters passed by reference.
     *
     * @param $interval
     * @param $unit
     *
     * @return void
     */
    protected function convertLength(&$interval, &$unit)
    {
        switch ($unit) {
            case Plan::INTERVAL_WEEKS:
                $interval = $interval * 7;
                $unit     = 'days';
                break;

            case Plan::INTERVAL_YEARS:
                $interval = $interval * 12;
                $unit     = 'months';
                break;
        }
    }
}
