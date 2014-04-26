<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

require_once __DIR__ . '/autoloader.php';
$loader = new Psr4AutoloaderClass();

$loader->register();
$loader->addNamespace('Simplerenew', __DIR__);
