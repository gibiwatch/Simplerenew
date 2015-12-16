<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/include.php';

SimplerenewHelperSite::loadTheme();

$controller = SimplerenewControllerBase::getInstance('Simplerenew');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
