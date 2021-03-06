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
use Simplerenew\Gateway\PlanInterface;

defined('_JEXEC') or die();

class Plan extends AbstractApiBase
{
    const INTERVAL_DAYS    = 'days';
    const INTERVAL_WEEKS   = 'weeks';
    const INTERVAL_MONTHS  = 'months';
    const INTERVAL_YEARS   = 'years';
    const INTERVAL_UNKNOWN = '?Error?';

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
    public $group = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var float
     */
    public $amount = null;

    /**
     * @var float
     */
    public $setup_cost = null;

    /**
     * @var int
     */
    public $length = null;

    /**
     * @var string
     */
    public $unit = null;

    /**
     * @var int
     */
    public $trial_length = null;

    /**
     * @var string
     */
    public $trial_unit = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var PlanInterface
     */
    protected $imp = null;

    /**
     * @param Configuration $config
     * @param PlanInterface $imp
     */
    public function __construct(Configuration $config, PlanInterface $imp)
    {
        parent::__construct($config);

        $this->imp = $imp;
    }

    /**
     * @param $code
     *
     * @return Plan
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
     * Get list of defined plans on the Gateway
     *
     * @return Plan[] Associative array of plans keyed on plan code
     * @throws Exception
     */
    public function getList()
    {
        $template = clone $this;
        $template->clearProperties();

        $plans = $this->imp->getList($template);
        ksort($plans);
        return $plans;
    }

    /**
     * @param bool $create
     *
     * @return Plan
     * @throws Exception
     */
    public function save($create = true)
    {
        $isNew = ($this->id <= 0);
        if ($isNew && !$create) {
            throw new Exception('Creating a new plan is not permitted in this context - ' . $this->code);
        }

        $this->imp->save($this);
        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        $this->imp->delete($this);
    }

    /**
     * Compare this plan's data to the passed data
     *
     * @param array|object $data
     *
     * @return bool
     * @throws Exception
     */
    public function equals($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (!is_array($data)) {
            throw new Exception('Incorrect argument passed');
        }
        $baseData = $this->getProperties();
        unset($baseData['created']);

        $data = array_intersect_key($data, $baseData);

        return ($baseData == $data);
    }

    /**
     * Determine if this plan is an upgrade
     *
     * @param string|Plan $fromPlan
     *
     * @return bool
     */
    public function isUpgradeFrom($fromPlan)
    {
        $isUpgrade     = false;
        $allowMultiple = $this->configuration->get('subscription.allowMultiple');
        $upgradeOrder  = $this->configuration->get('subscription.upgradeOrder');
        if (!$allowMultiple && $upgradeOrder) {
            try {
                if (!$fromPlan instanceof Plan) {
                    $planCode = $fromPlan;
                    $fromPlan = clone $this;
                    $fromPlan->load($planCode);
                }

                $fromLevel = (int)array_search($fromPlan->group, $upgradeOrder);
                $toLevel   = (int)array_search($this->group, $upgradeOrder);
                $isUpgrade = $fromLevel < $toLevel;

            } catch (Exception $e) {
                // This will count as not an upgrade
            }
        }
        return $isUpgrade;
    }
}
