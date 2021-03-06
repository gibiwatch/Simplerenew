<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Container;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

interface HandlerInterface
{
    /**
     * Execute a notify handler which should return a short string
     * explaining what was done if anything.
     *
     * @param Notify    $notice
     *
     * @return string
     */
    public function execute(Notify $notice);
}
