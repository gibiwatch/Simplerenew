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

defined('_JEXEC') or die();

class PlanImp extends AbstractRecurlyBase implements PlanInterface
{
    protected $fieldMap = array(
        'code'        => 'plan_code',
        'accountCode' => 'account_code',
        'length'      => 'plan_interval_length',
        'unit'        => array(
            'plan_interval_unit' => array(
                'months' => Plan::INTERVAL_MONTHS,
                'days'   => Plan::INTERVAL_DAYS,
                '::'     => Plan::INTERVAL_UNKNOWN
            )
        ),
        'trialLength' => 'trial_interval_length',
        'trialUnit'   => array(
            'trial_interval_unit' => array(
                'months' => Plan::INTERVAL_MONTHS,
                'days'   => Plan::INTERVAL_DAYS,
                '::'     => Plan::INTERVAL_UNKNOWN
            )
        ),
        'created'     => 'created_at'
    );

    protected $plansLoaded = array();

    public function load(Plan $parent)
    {
        if ($plan = $this->getPlan($parent->code)) {
            $parent->setProperties($plan, $this->fieldMap);

            $parent->setProperties(
                array(
                    'currency' => $this->currency,
                    'price'    => $this->getCurrency($plan->unit_amount_in_cents),
                    'setup'    => $this->getCurrency($plan->setup_fee_in_cents)
                )
            );
        }
    }

    public function save(Plan $parent)
    {

    }

    public function delete(Plan $parent)
    {

    }

    protected function getPlan($code)
    {
        if (!isset($this->plansLoaded[$code])) {
            try {
                $plan                     = \Recurly_Plan::get($code, $this->client);
                $this->plansLoaded[$code] = $plan;

            } catch (\Recurly_NotFoundError $e) {
                return null;

            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $this->plansLoaded[$code];
    }
}
