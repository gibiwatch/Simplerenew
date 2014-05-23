<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Prototype;

defined('_JEXEC') or die();

class Address extends AbstractPrototype
{
    /**
     * @var string
     */
    public $address1 = null;

    /**
     * @var string
     */
    public $address2 = null;

    /**
     * @var string
     */
    public $city = null;

    /**
     * @var string State/Province/Region
     */
    public $region = null;

    /**
     * @var string 2-letter ISO country code
     */
    public $country = null;

    /**
     * @var string
     */
    public $postal = null;
}
