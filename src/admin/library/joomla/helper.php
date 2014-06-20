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
            SimplerenewText::_('COM_SIMPLERENEW_SUBMENU_DASHBOARD'),
            'index.php?option=com_simplerenew&view=dashboard',
            $vName == 'dashboard'
        );

        self::addMenuEntry(
            SimplerenewText::_('COM_SIMPLERENEW_SUBMENU_PLANS'),
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
            try {
                $params = SimplerenewComponentHelper::getParams('com_simplerenew');

                $config = json_decode($params->toString(), true);
                $config['user']['adapter'] = 'joomla';
                self::$simplerenew = new \Simplerenew\Factory($config);
            } catch (Exception $e) {
                $app = SimplerenewFactory::getApplication();
                if ($app->isAdmin()) {
                    $link = 'index.php?option=com_simplerenew';
                } else {
                    $link = JRoute::_('index.php');
                }
                $app->redirect(
                    $link,
                    SimplerenewText::sprintf('COM_SIMPLERENEW_ERROR_CONFIGURATION', $e->getMessage()),
                    'error'
                );
            }

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
