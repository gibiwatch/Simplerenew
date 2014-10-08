<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
    protected $gatewayConfig = array();

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
        $this->gatewayConfig = $config;

        $this->currency = $config->get('currency', null);
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
        return $this->gatewayConfig->get($key, $default);
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        $cache = $this->gatewayConfig->get('cache');
        if (!$cache instanceof Cache) {
            $cache = new Cache(array('domain' => get_class($this)));
            $this->gatewayConfig->set('cache', $cache);
        }
        return $cache;
    }

    /**
     * @param Cache $cache
     *
     * @return AbstractGatewayBase
     */
    public function setCache(Cache $cache)
    {
        $this->gatewayConfig->set('cache', $cache);
        return $this;
    }

    /**
     * Get a unique domain key for cache items
     *
     * @param $key
     *
     * @return string
     */
    public function getCacheKey($key)
    {
        $domain = get_class($this);
        return $domain . '.' . $key;
    }

    /**
     * Determine whether the current configuration is usable/valid
     *
     * @return bool
     */
    abstract public function validConfiguration();
}
