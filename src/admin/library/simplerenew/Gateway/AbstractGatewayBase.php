<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractGatewayBase extends Object
{
    abstract public function __construct(array $config = array());
}
