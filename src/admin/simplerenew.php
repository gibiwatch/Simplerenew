<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/include.php';

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_simplerenew')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

require_once JPATH_COMPONENT . '/include.php';

// Load dependencies
jimport('joomla.application.component.controller');

$input      = JFactory::getApplication()->input;
$controller = JControllerLegacy::getInstance('Simplerenew');

$controller->execute($input->getCmd('task'));
$controller->redirect();
