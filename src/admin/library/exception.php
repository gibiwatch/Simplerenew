<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

class Exception extends \Exception
{
    public function getTraceMessage()
    {
        $caller = array_shift($this->getTrace());

        $result = '';
        if (!empty($caller['class'])) {
            $result .= $caller['class'] . '::';
        }
        if (!empty($caller['function'])) {
            $result .= $caller['function'] . '()';
        }

        return trim($result . ' ' . $this->message);
    }
}
