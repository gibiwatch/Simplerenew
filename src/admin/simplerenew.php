<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/include.php';

// Access check.
if (!SimplerenewFactory::getUser()->authorise('core.manage', 'com_simplerenew')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

$input = SimplerenewFactory::getApplication()->input;

// Check if configuration has been visited
if (!SimplerenewFactory::getStatus()->configured) {
    $input->set('view', 'welcome');
    $input->set('task', null);
}

$controller = SimplerenewControllerBase::getInstance('Simplerenew');
$controller->execute($input->getCmd('task'));
$controller->redirect();
