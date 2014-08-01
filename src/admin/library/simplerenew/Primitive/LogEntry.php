<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

use Simplerenew\Api\Notification;
use Simplerenew\Primitive\AbstractPrimitive;

defined('_JEXEC') or die();

class LogEntry extends AbstractPrimitive
{
    public $type = '';
    public $action = '';
    public $handler = '';
    public $package = '';
    public $ipaddress = '';
    public $logtime = '';
    public $user_id = '';
    public $account_code = '';
    public $subscription_id = '';

    public function __construct($data)
    {
        $this->setProperties($data);
        $this->ipaddress = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $this->logtime   = date('Y-m-d H:i:s');
    }
}
