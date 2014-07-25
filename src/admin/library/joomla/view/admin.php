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
            $hide    = SimplerenewFactory::getApplication()->input->getBool('hidemainmenu', false);
            $sidebar = count(JHtmlSidebar::getEntries()) + count(JHtmlSidebar::getFilters());
            if (!$hide && $sidebar > 0) {
                $start = array(
                    '<div id="j-sidebar-container" class="span2">',
                    JHtmlSidebar::render(),
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

        $this->displayFooter();
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
            $doc = SimplerenewFactory::getDocument();
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
        $user = SimplerenewFactory::getUser();
        if ($user->authorise('core.admin', 'com_simplerenew')) {
            if ($addDivider) {
                JToolBarHelper::divider();
            }
            JToolBarHelper::preferences('com_simplerenew');
        }
    }

    /**
     * Render a form fieldset with the ability to compact two fields
     * into a single line
     *
     * @param string $fieldSet
     * @param array  $sameLine
     *
     * @return string
     */
    protected function renderFieldset($fieldSet, array $sameLine = array())
    {
        $html = array();
        if (!empty($this->form) && $this->form instanceof JForm) {
            $fieldSets = $this->form->getFieldsets();

            if (!empty($fieldSets[$fieldSet])) {
                $name  = $fieldSets[$fieldSet]->name;
                $label = $fieldSets[$fieldSet]->label ? : 'COM_SIMPLERENEW_ADMIN_PAGE_' . $name;

                $joomla3 = version_compare(JVERSION, '3', 'ge');
                if ($joomla3) {
                    $html = array(
                        JHtml::_('bootstrap.addTab', 'myTab', $name, JText::_($label)),
                        '<div class="row-fluid">',
                        '<fieldset class="adminform">'
                    );

                } else {
                    $html = array(
                        JHtml::_('tabs.panel', JText::_($label), $name . '-page'),
                        '<div class="width-100">',
                        '<fieldset class="adminform">',
                        '<ul class="adminformlist">'
                    );
                }

                foreach ($this->form->getFieldset($name) as $field) {
                    if (in_array($field->fieldname, $sameLine)) {
                        continue;
                    }

                    if ($joomla3) {
                        $fieldHtml = array(
                            '<div class="control-group">',
                            '<div class="control-label">',
                            $field->label,
                            '</div>',
                            '<div class="controls">',
                            $field->input
                        );

                    } else {
                        $fieldHtml = array(
                            '<li>' . $field->label . $field->input . '</li>'
                        );
                    }

                    $html = array_merge($html, $fieldHtml);

                    if (isset($sameLine[$field->fieldname])) {
                        $html[] = ' ' . $this->form->getField($sameLine[$field->fieldname])->input;
                    }

                    if ($joomla3) {
                        $html[] = '</div>';
                        $html[] = '</div>';
                    }
                }
                if ($joomla3) {
                    $endHtml = array(
                        '</fieldset>',
                        '</div>',
                        JHtml::_('bootstrap.endTab')
                    );

                } else {
                    $endHtml = array(
                        '</ul>',
                        '</fieldset>',
                        '</div>',
                        '<div class="clr"></div>'
                    );
                }

                $html = array_merge($html, $endHtml);
            }
        }
        return join("\n", $html);
    }

    /**
     * Display a standard footer on all admin pages
     *
     * @return void
     */
    protected function displayFooter()
    {
        $info = SimplerenewHelper::getInfo()->toObject();

        echo JText::sprintf(
            'COM_SIMPLERENEW_VERSION_FOOTER',
            JText::_($info->name),
            $info->version,
            $info->creationDate,
            $info->copyright,
            $info->authorEmail
        );
    }
}
