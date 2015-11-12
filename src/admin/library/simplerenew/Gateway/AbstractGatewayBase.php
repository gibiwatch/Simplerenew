<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Object;

defined('_JEXEC') or die();

/**
 * Class AbstractGatewayBase
 *
 * @package Simplerenew\Gateway
 */
abstract class AbstractGatewayBase extends Object
{
    /**
     * @var Configuration
     */
    protected $configuration = array();

    /**
     * @var string
     */
    protected $currency = null;

    public function __construct(Configuration $config = null)
    {
        $this->configuration = $config;

        $this->currency = $config->get('currency');
    }

    /**
     * Convenience method for retrieving gateway config items
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getCfg($key, $default = null)
    {
        return $this->configuration->get('gateway.' . $key, $default);
    }

    protected function setCfg($key, $value)
    {
        return $this->configuration->set('gateway.' . $key, $value);
    }

    /**
     * Normalize a variable to a \DateTime object
     *
     * @param mixed $value
     *
     * @return \DateTime|null
     */
    protected function toDateTime($value)
    {
        if ($value && is_string($value)) {
            return new \DateTime($value);

        } elseif ($value instanceof \DateTime) {
            return $value;
        }

        return null;
    }

    /**
     * Normalize a variable to a SQL date/time string
     *
     * @param string|\DateTime $value
     *
     * @return string
     */
    protected function fromDateTime($value)
    {
        if (is_string($value)) {
            $value = new \DateTime($value);
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d h:i:s');
        }

        return null;
    }

    /**
     * Determine whether the current configuration is usable/valid
     *
     * @return bool
     */
    abstract public function validConfiguration();
}
