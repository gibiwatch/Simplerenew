<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_ADMIN')) {
    define('SIMPLERENEW_ADMIN', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
}
if (!defined('SIMPLERENEW_SITE')) {
    define('SIMPLERENEW_SITE', JPATH_SITE . '/components/com_simplerenew');
}

// Iinitialise and register the autoloader and paths
require_once SIMPLERENEW_ADMIN . '\helpers\autoloader.php';
$loader = new \Simplerenew\Psr4AutoloaderClass();

$loader->register();
$loader->addNamespace('Simplerenew', SIMPLERENEW_ADMIN  . '/lib');

if (!defined('FOF_INCLUDED')) {
    require_once JPATH_LIBRARIES . '/fof/include.php';
}

// Register exceptions to loader rules
JLoader::register('SimplerenewHelper', SIMPLERENEW_ADMIN . '/helpers/simplerenew.php');

// Add standard paths
//JTable::addIncludePath(OSCLASSROOM_ADMIN . '/tables');
//JHtml::addIncludePath(OSCLASSROOM_ADMIN . '/helpers/html');

// Ignore the rest if we're running as a CLI
if (!defined('SIMPLERENEW_CLI')) {
    switch (JFactory::getApplication()->getName()) {
        case 'administrator':
            break;

        case 'site':
//            jimport('joomla.application.component.model');

//            JTable::addIncludePath(SIMPLERENEW_SITE . '/tables');
//            JModel::addIncludePath(SIMPLERENEW_SITE . '/models');

            break;
    }
}