<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Plugin;

defined('_JEXEC') or die();

interface CmsInterface
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
