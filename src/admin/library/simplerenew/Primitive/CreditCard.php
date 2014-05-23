<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

defined('_JEXEC') or die();

class CreditCard extends AbstractPrimitive
{
    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $number = null;

    /**
     * @var string
     */
    public $month = null;

    /**
     * @var string
     */
    public $year = null;

    /**
     * @var string
     */
    public $cvv = null;

    /**
     * @var Address
     */
    protected $address = null;
}
