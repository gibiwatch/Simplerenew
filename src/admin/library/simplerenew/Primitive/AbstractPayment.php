<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

defined('_JEXEC') or die();

abstract class AbstractPayment extends AbstractPrimitive
{
    /**
     * Determine if the payment type exists
     *
     * @return bool
     */
    abstract public function exists();
}
