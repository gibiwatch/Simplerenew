<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    define('SIMPLERENEW_LOADED', 1);
    define('SIMPLERENEW_ADMIN', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
    define('SIMPLERENEW_SITE', JPATH_SITE . '/components/com_simplerenew');
    define('SIMPLERENEW_MEDIA', JPATH_SITE . '/media/com_simplerenew');
    define('SIMPLERENEW_LIBRARY', SIMPLERENEW_ADMIN . '/library');

    // Setup autoloaded libraries
    require_once SIMPLERENEW_LIBRARY . '/psr4autoloader.php';
    $loader = new Psr4AutoloaderClass();

    $loader->register();
    $loader->addNamespace('Simplerenew', SIMPLERENEW_LIBRARY . '/simplerenew');

    // Set the Joomla overrides loader
    require_once SIMPLERENEW_LIBRARY . '/joomla/loader.php';

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
