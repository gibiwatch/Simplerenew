<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

defined('_JEXEC') or die();

define('OSCLASSROOM_ADMIN', JPATH_ADMINISTRATOR . '/components/com_osclassroom');
define('OSCLASSROOM_SITE', JPATH_SITE . '/components/com_osclassroom');

require_once OSCLASSROOM_ADMIN . '/helpers/loader.php';

// Register exceptions to loader rules
JLoader::register('OsclassroomHelper', OSCLASSROOM_ADMIN . '/helpers/osclassroom.php');

// Add standard paths
JTable::addIncludePath(OSCLASSROOM_ADMIN . '/tables');
JHtml::addIncludePath(OSCLASSROOM_ADMIN . '/helpers/html');

// Ignore the rest if we're running as a CLI
if (!defined('OSCLASSROOM_CLI')) {
    // Initialize com_recurly because we're so linked to it
    if (!defined('RECURLY_ADMIN')) {
        require_once JPATH_ADMINISTRATOR . '/components/com_recurly/helpers/initialize.php';
    }

    switch (JFactory::getApplication()->getName()) {
        case 'administrator':
            break;

        case 'site':
            jimport('joomla.application.component.model');

            JTable::addIncludePath(OSCLASSROOM_SITE . '/tables');
            JModel::addIncludePath(RECURLY_SITE . '/models');

            if (JFactory::getApplication()->input->getCmd('option') == 'com_osclassroom') {
                JHtml::_('stylesheet', 'com_osclassroom/osclassroom.css', null, true);
            }
            break;
    }
}
