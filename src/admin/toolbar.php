<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewToolbar extends FOFToolbar
{
    /**
     * Prepares the toolbar for Cpanel view
     *
     * @return void
     */
    public function onCpanelsBrowse()
    {
        // Set the toolbar title
        JToolBarHelper::title(JText::_('COM_SIMPLERENEW'), 'simplerenew');

        // Add Components options (see config.xml)
        JToolBarHelper::preferences('com_simplerenew');
    }
}
