<?php
/**
 * @package   tests_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * Loads the necessary support for testing Simple Renew
 */

define('SIMPLERENEW_TESTS', __DIR__);

// Load local installation configurations
$configPath = SIMPLERENEW_TESTS . '/config.php';
if (!file_exists($configPath)) {
    throw new Exception('Local configuration was not found: ' . $configPath);
}
require_once $configPath;

$pathToJoomla = $testPaths['joomla'];
if (!is_dir($pathToJoomla)) {
    throw new Exception('Could not find the Joomla folder: ' . $pathToJoomla);
}
$pathToJoomla = realpath($pathToJoomla);

// Load a minimal Joomla framework
define('_JEXEC', 1);

if (!defined('JPATH_BASE')) {
    define('JPATH_BASE', realpath($pathToJoomla));
}
require_once JPATH_BASE . '/includes/defines.php';

require_once JPATH_BASE . '/includes/framework.php';

// Copied from /includes/framework.php
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

require_once JPATH_LIBRARIES . '/import.php';

error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

// Force library to be in JError legacy mode
JError::$legacy = true;
JError::setErrorHandling(E_NOTICE, 'message');
JError::setErrorHandling(E_WARNING, 'message');
JError::setErrorHandling(E_ERROR, 'message');

jimport('joomla.application.menu');
jimport('joomla.environment.uri');
jimport('joomla.utilities.utility');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.arrayhelper');

// Bootstrap the CMS libraries.
if (!defined('JPATH_PLATFORM')) {
    define('JPATH_PLATFORM', JPATH_BASE . '/libraries');
}
if (!defined('JDEBUG')) {
    define('JDEBUG', false);
}
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

// Instantiate some needed objects
JFactory::getApplication('site');

// Bootstrap Simple Renew
define('SIMPLERENEW_SRC', realpath(SIMPLERENEW_TESTS . '/../src'));
if (!is_dir(SIMPLERENEW_SRC)) {
    throw new Exception('Could not find the Simple Renew folder: ' . SIMPLERENEW_SRC);
}

// Specialized initialisation for Simple Renew testing
define('SIMPLERENEW_LOADED', 1);
define('SIMPLERENEW_ADMIN', SIMPLERENEW_SRC . '/admin');
define('SIMPLERENEW_SITE', SIMPLERENEW_SRC . '/site');
define('SIMPLERENEW_MEDIA', SIMPLERENEW_SRC . '/media');
define('SIMPLERENEW_LIBRARY', SIMPLERENEW_ADMIN . '/library');

// Setup autoloaded libraries
require_once SIMPLERENEW_LIBRARY . '/simplerenew/AutoLoader.php';
Simplerenew\AutoLoader::register('Simplerenew', SIMPLERENEW_LIBRARY . '/simplerenew');
Simplerenew\AutoLoader::registerCamelBase('Simplerenew', SIMPLERENEW_LIBRARY . '/joomla');
