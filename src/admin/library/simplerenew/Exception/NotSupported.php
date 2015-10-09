<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Exception;

use Simplerenew\Exception;

defined('_JEXEC') or die();

class NotSupported extends Exception
{
    public function __construct($message = "", $code = 409, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
