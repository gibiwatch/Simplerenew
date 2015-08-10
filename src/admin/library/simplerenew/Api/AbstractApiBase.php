<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Gateway\AbstractGatewayBase;
use Simplerenew\Object;
use Simplerenew\Plugin\Events;

defined('_JEXEC') or die();

abstract class AbstractApiBase extends Object
{
    /**
     * @var AbstractGatewayBase
     */
    protected $imp = null;

    /**
     * @var Configuration
     */
    protected $configuration = null;

    /**
     * @var Events
     */
    protected $events = null;

    public function __construct()
    {

    }

    /**
     * Map raw data from the Gateway to SR fields in $this
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function bindSource($data)
    {
        if (method_exists($this->imp, 'bindSource')) {
            $this->imp->bindSource($this, $data);
        } else {
            $this->setProperties($data);
        }
        return $this;
    }

    /**
     * Test for valid configuration of the gateway.
     *
     * @return bool
     */
    public function validConfiguration()
    {
        if ($this->imp && $this->imp instanceof AbstractGatewayBase) {
            return $this->imp->validConfiguration();
        }
        return false;
    }

    /**
     * Get the name of the Gateway being used
     *
     * @return string|null
     */
    public function getGatewayName()
    {
        if ($this->imp) {
            $refClass    = new \ReflectionClass($this->imp);
            $namespace = explode('\\', $refClass->getNamespaceName());
            return array_pop($namespace);
        }

        return null;
    }
}
