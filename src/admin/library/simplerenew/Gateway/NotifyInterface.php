<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Exception;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

interface NotifyInterface
{
    /**
     * Translate a notification message from the gateway into
     * a Notify object. All validation of the message and its
     * source should be done here.
     *
     * @param string $package
     *
     * @return array
     * @throws Exception
     */
    public function loadPackage($package);
}
