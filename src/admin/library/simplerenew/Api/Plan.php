<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

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
    public $setup = null;

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
    public $trialLength = null;

    /**
     * @var string
     */
    public $trialUnit = null;

    /**
     * @var string
     */
    public $accountCode = null;

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

    public function load($code)
    {
        $this->clearProperties();
        $this->code = $code;
        $this->imp->load($this);

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
}
