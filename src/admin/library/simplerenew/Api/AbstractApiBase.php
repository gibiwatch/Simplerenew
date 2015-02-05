<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Container;
use Simplerenew\Exception;
use Simplerenew\Gateway\AbstractGatewayBase;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractApiBase extends Object
{
    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var AbstractGatewayBase
     */
    protected $imp = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
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
}
