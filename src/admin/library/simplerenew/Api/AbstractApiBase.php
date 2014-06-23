<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractApiBase extends Object
{
    /**
     * @throws Exception
     */
    public function save()
    {
        throw new Exception('Saving to gateway is not supported');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        throw new Exception('Deleting from gateway is not supported');
    }
}
