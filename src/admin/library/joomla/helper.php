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
     * Save submitted form data in the session.
     * NOTE: No security is employed here. Caller should be
     * careful not to store sensitive data in the session.
     * Avoid storing passwords and credit card data.
     *
     * @param string $domain
     * @param array  $source
     *
     * @return void
     */
    public static function saveFormData($domain, array $source = null)
    {
        $domain = 'simplerenew.' . $domain;

        if ($source === null) {
            $source = $_REQUEST;
        }
        $app = SimplerenewFactory::getApplication();

        $app->setUserState($domain, base64_encode(serialize($source)));
    }

    /**
     * Retrieve previously saved form data to repopulate forms
     *
     * @param string $domain
     * @param bool   $clear
     *
     * @return array
     */
    public static function loadFormData($domain, $clear = true)
    {
        $domain = 'simplerenew.' . $domain;
        $app    = SimplerenewFactory::getApplication();
        $formData = unserialize(base64_decode($app->getUserState($domain)));

        if ($clear) {
            $app->setUserState($domain, null);
        }
        return $formData;
    }

    /**
     * @param string $name
     * @param string $link
     * @param bool   $active
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
