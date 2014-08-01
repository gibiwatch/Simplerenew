<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Notify\Handler;

use Simplerenew\Container;
use Simplerenew\Notify\Notify;

defined('_JEXEC') or die();

interface HandlerInterface
{
    /**
     * @param Notify    $notice
     * @param Container $container
     *
     * @return mixed
     */
    public function execute(Notify $notice, Container $container);
}
