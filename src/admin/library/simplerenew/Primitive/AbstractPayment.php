<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
