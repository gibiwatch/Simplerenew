<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

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
    public $description = null;

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
     * @var string
     */
    public $accounting_code = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var PlanInterface
     */
    protected $imp = null;

    /**
     * @param PlanInterface $imp
     * @param array         $config
     */
    public function __construct(PlanInterface $imp, array $config = array())
    {
        $this->imp = $imp;
    }

    /**
     * @param $code
     *
     * @return Plan
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
     * @return array Associative array of plans keyed on plan code
     */
    public function getList()
    {
        $template = clone $this;
        $template->clearProperties();

        $plans = $this->imp->getList($template);
        return $plans;
    }

    /**
     * @param bool $create
     *
     * @return $this
     * @throws Exception
     */
    public function save($create = false)
    {
        $isNew = ($this->id <= 0);
        if ($isNew && !$create) {
            throw new Exception('Creating a new plan is not permitted in this context - ' . $this->code);
        }

        $this->imp->save($this);
        $this->imp->load($this);

        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        $this->imp->delete($this);
        $this->imp->load($this);
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

        $data             = array_intersect_key($data, $baseData);
        $data['currency'] = @$data['currency'] ? : $this->currency;
        if (!empty($data['created'])) {
            unset($data['created']);
        }

        return ($baseData == $data);
    }
}
