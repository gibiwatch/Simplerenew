<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Gateway\GatewayBase;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class ApiBase extends Object
{
    protected $imp = null;

    public function __construct(GatewayBase $imp)
    {
        $this->imp = $imp;
    }
}
