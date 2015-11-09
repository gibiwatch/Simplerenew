<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Exception;

use Simplerenew\Exception;

defined('_JEXEC') or die();

class Duplicate extends Exception
{
    public function __construct($message = "", $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
