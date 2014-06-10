<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewAdminView extends JViewLegacy
{
    public function __construct($config = array())
    {
        if (empty($config['layout'])) {
            $config['layout'] = 'default';
        }
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $config['layout'] .= '.j3';
        }

        parent::__construct($config);
    }

    protected function setTitle($sub = null, $icon = 'simplerenew')
    {
        $img = JHtml::_('image', "com_simplerenew/icon-48-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = JFactory::getDocument();
            $doc->addStyleDeclaration(".icon-48-{$icon} { background-image: url({$img}); }");
        }

        $title = JText::_('COM_SIMPLERENEW');
        if ($sub) {
            $title .= ': ' . JText::_($sub);
        }

        JToolbarHelper::title($title, $icon);
    }

    protected function setToolBar($addDivider = true)
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.admin', 'com_simplerenew')) {
            if ($addDivider) {
                JToolBarHelper::divider();
            }
            JToolBarHelper::preferences('com_simplerenew');
        }
    }
}
