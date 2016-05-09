<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla;

use JEventDispatcher;
use Simplerenew\Plugin\CmsInterface;

defined('_JEXEC') or die();

class Events implements CmsInterface
{
    /**
     * @var JEventDispatcher
     */
    protected $dispatcher = null;

    public function __construct()
    {
        $this->dispatcher = JEventDispatcher::getInstance();
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
