<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms;

defined('_JEXEC') or die();

interface CmsEventsInterface
{
    /**
     * Translate SR events into registered native CMS events
     *
     * @param string $event
     * @param array  $arguments
     *
     * @return array
     */
    public function trigger($event, array $arguments = array());
}
