<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/include.php';
JHtml::_('stylesheet', 'com_simplerenew/admin.css', null, true);

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
