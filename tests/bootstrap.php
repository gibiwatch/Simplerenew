<?php
/**
 * Loads the necessary support for testing Simple Renew
 */

// Load local installation configurations
$configPath = __DIR__ . '/config.php';
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
require_once JPATH_BASE.'/includes/defines.php';

// Copied from /includes/framework.php
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

require_once JPATH_LIBRARIES.'/import.php';

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
require_once JPATH_LIBRARIES.'/cms.php';

// Instantiate some needed objects
JFactory::getApplication('site');

// Bootstrap Simple Renew
define('SIMPLERENEW_TEST', realpath(__DIR__ . '/../src'));

if (!is_dir(SIMPLERENEW_TEST)) {
    throw new Exception('Could not find the Simple Renew folder: ' . SIMPLERENEW_TEST);
}

// Check to make sure FOF is loaded
$pathToFOF = $testPaths['fof'];
if (empty($pathToFOF)) {
    $pathToFOF = JPATH_LIBRARIES . '/fof/include.php';
}
if (!file_exists($pathToFOF)) {
    $pathToFOF = SIMPLERENEW_TEST . '/assets/fof/include.php';
}
require_once $pathToFOF;

// Specialized initialisation for Simple Renew testing
define('SIMPLERENEW_ADMIN', SIMPLERENEW_TEST . '/admin');
define('SIMPLERENEW_SITE', SIMPLERENEW_TEST . '/site');
require_once SIMPLERENEW_ADMIN . '/helpers/initialise.php';