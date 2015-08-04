<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla;

use Simplerenew\Plugin\CmsInterface;

defined('_JEXEC') or die();

class Events implements CmsInterface
{
    /**
     * @var \JEventDispatcher|\JDispatcher
     */
    protected $dispatcher = null;

    public function __construct()
    {
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $this->dispatcher = \JEventDispatcher::getInstance();
        } else {
            $this->dispatcher = \JDispatcher::getInstance();
        }
    }

    /**
     * Translate SR events into registered native CMS events
     *
     * @param string $event
     * @param array  $arguments
     *
     * @return array
     */
    public function trigger($event, array $arguments = array())
    {
        return $this->dispatcher->trigger($event, $arguments);
    }
}
