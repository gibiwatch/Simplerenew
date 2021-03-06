<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\AutoLoader;

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    define('SIMPLERENEW_LOADED', 1);
    define('SIMPLERENEW_ADMIN', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
    define('SIMPLERENEW_SITE', JPATH_SITE . '/components/com_simplerenew');
    define('SIMPLERENEW_MEDIA', JPATH_SITE . '/media/com_simplerenew');
    define('SIMPLERENEW_LIBRARY', SIMPLERENEW_ADMIN . '/library');

    // Setup all autoloading
    require_once SIMPLERENEW_ADMIN . '/vendor/autoload.php';
    require_once SIMPLERENEW_LIBRARY . '/simplerenew/AutoLoader.php';

    AutoLoader::register('Simplerenew', SIMPLERENEW_LIBRARY . '/simplerenew');
    AutoLoader::registerCamelBase('Simplerenew', SIMPLERENEW_LIBRARY . '/joomla');

    // Additional helper paths
    JHtml::addIncludePath(SIMPLERENEW_LIBRARY . '/html');
    SimplerenewHelper::loadOptionLanguage('com_simplerenew', SIMPLERENEW_ADMIN, SIMPLERENEW_SITE);

    SimplerenewAddon::load();
}
