<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelper
{
    /**
     * @var \Simplerenew\Factory
     */
    protected static $simplerenew = null;

    public static function addSubmenu($vName)
    {
        self::addMenuEntry(
            JText::_('COM_SIMPLERENEW_SUBMENU_DASHBOARD'),
            'index.php?option=com_simplerenew&view=dashboard',
            $vName == 'dashboard'
        );

        self::addMenuEntry(
            JText::_('COM_SIMPLERENEW_SUBMENU_PLANS'),
            'index.php?option=com_simplerenew&view=plans',
            $vName == 'plans'
        );
    }

    /**
     * Get the Simplerenew Factory class
     *
     * @TODO: Review Factory/DI pattern for possible improvement
     *
     * @return \Simplerenew\Factory
     */
    public static function getSimplerenew()
    {
        if (!self::$simplerenew instanceof \Simplerenew\Factory) {
            $params = JComponentHelper::getParams('com_simplerenew');

            $config = json_decode($params->toString(), true);
            $config['user']['adapter'] = 'joomla';
            self::$simplerenew = new \Simplerenew\Factory($config);
        }
        return self::$simplerenew;
    }

    /**
     * @param string $name
     * @param string $link
     * @param bool $active
     *
     * @return void
     */
    protected static function addMenuEntry($name, $link, $active = false)
    {
        if (version_compare(JVERSION, '3.0', 'ge')) {
            JHtmlSidebar::addEntry($name, $link, $active);
        } else {
            JSubMenuHelper::addEntry($name, $link, $active);
        }
    }
}
