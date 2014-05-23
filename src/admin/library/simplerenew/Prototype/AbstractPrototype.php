<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Prototype;

use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractPrototype extends Object
{
    public function __clone()
    {
        $properties = array_keys($this->getProperties());
        foreach ($properties as $property) {
            $this->$property = null;
        }
    }
}
