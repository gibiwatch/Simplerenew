<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Configuration;
use Simplerenew\Cache;
use Simplerenew\Object;

defined('_JEXEC') or die();

/**
 * Class AbstractGatewayBase
 * @package Simplerenew\Gateway
 *
 * @property Cache $cache
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

    /**
     * @var Cache
     */
    private $cache = null;

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
        return $this->configuration->get('gateway.recurly.' . $key, $default);
    }

    protected function setCfg($key, $value)
    {
        return $this->configuration->set('gateway.recurly.' . $key, $value);
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
     * Determine whether the current configuration is usable/valid
     *
     * @return bool
     */
    abstract public function validConfiguration();
}
