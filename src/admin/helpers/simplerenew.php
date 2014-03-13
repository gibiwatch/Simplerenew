<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelper
{
    public static function addSubmenu($view)
    {
        JSubMenuHelper::addEntry(
            JText::_('COM_SIMPLERENEW_SUBMENU_XX'),
            'index.php?option=com_simplerenew&view=classes',
            $view == 'xx');
    }
}
