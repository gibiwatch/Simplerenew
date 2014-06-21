<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

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
     * @var array
     */
    protected $gatewayConfig = array();

    /**
     * @var Cache
     */
    private $cache = null;

    public function __construct(array $config = array())
    {
        if (!empty($config['cache']) && $config['cache'] instanceof Cache) {
            $this->cache = clone $config['cache'];
            $this->cache->setDomain(get_class($this));
            unset($config['cache']);
        }

        $this->gatewayConfig = $config;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        if (!$this->cache instanceof Cache) {
            $this->cache = new Cache(array('domain' => get_class($this)));
        }
        return $this->cache;
    }

    /**
     * @param Cache $cache
     *
     * @return AbstractGatewayBase
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function getCacheKey($key)
    {
        $domain = get_class($this);
        return $domain . '.' . $key;
    }
}
