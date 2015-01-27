<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\AutoLoader;

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    define('SIMPLERENEW_LOADED', 1);
    define('SIMPLERENEW_ADMIN', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
    define('SIMPLERENEW_SITE', JPATH_SITE . '/components/com_simplerenew');
    define('SIMPLERENEW_MEDIA', JPATH_SITE . '/media/com_simplerenew');
    define('SIMPLERENEW_LIBRARY', SIMPLERENEW_ADMIN . '/library');

    // Setup autoload libraries
    require_once SIMPLERENEW_LIBRARY . '/alledia/AutoLoader.php';
    AutoLoader::register('Simplerenew', SIMPLERENEW_LIBRARY . '/simplerenew');
    AutoLoader::registerCamelBase('Simplerenew', SIMPLERENEW_LIBRARY . '/joomla');

    // Any additional helper paths
    JHtml::addIncludePath(SIMPLERENEW_LIBRARY . '/html');

    // Cover other situations
    if (SimplerenewFactory::getApplication()->input->getCmd('option') != 'com_simplerenew') {
        switch (JFactory::getApplication()->getName()) {
            case 'administrator':
                SimplerenewFactory::getLanguage()->load('com_simplerenew', SIMPLERENEW_ADMIN);
                break;

            case 'site':
                SimplerenewFactory::getLanguage()->load('com_simplerenew', SIMPLERENEW_SITE);
                break;
        }
    }
}
