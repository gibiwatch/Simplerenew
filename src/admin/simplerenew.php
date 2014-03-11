<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

//require_once JPATH_LIBRARIES . '/fof/include.php';

require_once __DIR__ . '/helpers/install.php';

$install = new SimplerenewInstall();

$install->packages(__DIR__ . '/helpers/packages.xml', __DIR__.'/additions');

