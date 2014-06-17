<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewViewAdmin extends JViewLegacy
{
    public function display($tpl = null)
    {
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $hide = JFactory::getApplication()->input->getBool('hidemainmenu', false);
            if (!$hide && $sidebar = JHtmlSidebar::render()) {
                $start = array(
                    '<div id="j-sidebar-container" class="span2">',
                    $sidebar,
                    '</div>',
                    '<div id="j-main-container" class="span10">'
                );
            } else {
                $start = array(
                    '<div id="j-main-container">'
                );
            }

            echo join("\n", $start) . "\n";
            parent::display($tpl);
            echo "\n</div>";
        } else {
            parent::display($tpl);
        }
    }

    public function getLayout()
    {
        $layout = parent::getLayout();
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $layout .= '.j2';
        }
        return $layout;
    }

    /**
     * Default admin screen title
     *
     * @param string $sub
     * @param string $icon
     *
     * @return void
     */
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

    /**
     * Render the admin screen toolbar buttons
     *
     * @param bool $addDivider
     *
     * @return void
     */
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

    protected function getSortFields()
    {
        return array();
    }
}
