<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify;

use Simplerenew\Primitive\AbstractLogEntry;

defined('_JEXEC') or die();

class LogEntry extends AbstractLogEntry
{
    /**
     * @var string
     */
    public $type = '';

    /**
     * @var string
     */
    public $action = '';

    /**
     * @var string
     */
    public $handler = '';

    /**
     * @var string
     */
    public $response = '';

    /**
     * @var string
     */
    public $package = '';

    /**
     * @var string
     */
    public $ipaddress = '';

    /**
     * @var string
     */
    public $logtime = '';

    /**
     * @var string
     */
    public $user_id = '';

    /**
     * @var string
     */
    public $account_code = '';

    /**
     * @var string
     */
    public $subscription_id = '';

    public function __construct($data)
    {
        parent::__construct($data);

        $this->ipaddress = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $this->logtime   = date('Y-m-d H:i:s');
    }
}
