<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractPrimitive extends Object
{
    public function __construct($data = null)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            $properties = $this->getProperties();
            foreach ($properties as $k => $v) {
                if (isset($data[$k])) {
                    $this->$k = $data[$k];
                }
            }
        }
    }
}
