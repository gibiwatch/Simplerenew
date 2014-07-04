<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Gateway\AbstractGatewayBase;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractApiBase extends Object
{
    /**
     * Test for valid configuration. Assumes that the implementation
     * class is set to $this->imp. Returns false by default.
     *
     * @return bool
     */
    public function validConfiguration()
    {
        if (!empty($this->imp) && $this->imp instanceof AbstractGatewayBase) {
            return $this->imp->validConfiguration();
        }
        return false;
    }
}
