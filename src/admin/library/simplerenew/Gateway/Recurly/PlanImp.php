<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Plan;
use Simplerenew\Exception;
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

    public function load(Plan $parent)
    {
        if ($plan = $this->getPlan($parent->code)) {
            $this->bindToPlan($plan, $parent);
        }
    }

    /**
     * Set the API object properties from the native Recurly object
     *
     * @param string $plan
     * @param Plan   $target
     */
    protected function bindToPlan($plan, Plan $target)
    {
        $target->setProperties($plan, $this->fieldMap);

        $target->setProperties(
            array(
                'currency' => $this->currency,
                'amount'   => $this->getCurrency($plan->unit_amount_in_cents),
                'setup'    => $this->getCurrency($plan->setup_fee_in_cents)
            )
        );
    }

    public function getList(Plan $template)
    {
        $this->plansLoaded = array();
        $rawObjects        = \Recurly_PlanList::get(null, $this->client);

        foreach ($rawObjects as $plan) {
            $nextPlan = clone $template;
            $this->bindToPlan($plan, $nextPlan);
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
            return new \Recurly_Plan(null, $this->client);
        }

        if (!isset($this->plansLoaded[$code])) {
            try {
                $plan = \Recurly_Plan::get($code, $this->client);

            } catch (\Recurly_NotFoundError $e) {
                $plan = new \Recurly_Plan(null, $this->client);
                $plan->plan_code = $code;

            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }

            $this->plansLoaded[$plan->code] = $plan;
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
        $plan  = $this->getPlan($parent->code);
        $isNew = !(bool)$plan->created_at;

        $plan->name                  = $parent->name;
        $plan->description           = $parent->description;
        $plan->plan_interval_length  = $parent->length;
        $plan->plan_interval_unit    = $parent->unit;
        $plan->trial_interval_length = $parent->trial_length;
        $plan->trial_interval_unit   = $parent->trial_unit;
        $plan->accounting_code       = $parent->accounting_code;

        $amount     = $parent->amount * 100;
        $setup_cost = $parent->setup_cost * 100;
        if ($isNew) {
            $plan->unit_amount_in_cents->addCurrency($parent->currency, $amount);
            $plan->setup_fee_in_cents->addCurrency($parent->currency, $setup_cost);

            $plan->create();
        } else {
            $plan->unit_amount_in_cents[$parent->currency] = $amount;
            $plan->setup_fee_in_cents[$parent->currency]   = $setup_cost;

            $plan->update();
        }
    }

    /**
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Plan $parent)
    {
        $plan = $this->getPlan($parent->code);
        $plan->delete();
    }
}
