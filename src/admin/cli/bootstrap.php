<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * This bootstrap file can be used by any php script to initialise
 * everything needed to use the classes and methods of Simple Renew.
 * See example.php for an example of a cli script using this
 */

// Make sure we're being called from the command line, not a web interface
if (PHP_SAPI !== 'cli') {
    throw new Exception('This is a command line only application.');
}

error_reporting(-1);
ini_set('display_errors', 1);

// Initialize Joomla framework
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

// Load defines
$basePath = realpath(__DIR__ . str_repeat('/..', 4));
if (file_exists($basePath . '/defines.php')) {
    require_once $basePath . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', $basePath);
    require_once JPATH_BASE . '/includes/defines.php';
}

if (is_file(JPATH_LIBRARIES . '/import.legacy.php')) {
    // Joomla 3.x setup
    require_once JPATH_LIBRARIES . '/import.legacy.php';
} else {
    // Joomla 2.5 setup
    require_once JPATH_LIBRARIES . '/import.php';

    // Load things that don't autoload
    jimport('joomla.application.component.helper');
    jimport('joomla.database.table');
    jimport('joomla.environment.uri');
    jimport('joomla.event.dispatcher');

    // Force library to be in JError legacy mode
    JError::$legacy = true;
}
require_once JPATH_LIBRARIES . '/cms.php';
require_once JPATH_CONFIGURATION . '/configuration.php';

// Pretend to be an admin application for plugins
$_SERVER['HTTP_HOST'] = 'localhost';
JFactory::getApplication('administrator');

// Init SRSubs
require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
JPluginHelper::importPlugin('simplerenew');
