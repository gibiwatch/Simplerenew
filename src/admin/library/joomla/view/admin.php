<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewViewAdmin extends SimplerenewView
{
    /**
     * @var JObject
     */
    protected $state = null;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->state = $this->get('State');
    }

    /**
     * Admin display to handle differences between Joomla 2.5 and 3.x
     *
     * @param null $tpl
     *
     * @throws Exception
     * @return void
     */
    public function display($tpl = null)
    {
        $this->displayHeader();

        if (version_compare(JVERSION, '3.0', 'lt')) {
            parent::display($tpl);

        } else {
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
                $start = array('<div id="j-main-container">');
            }

            echo join("\n", $start) . "\n";
            parent::display($tpl);
            echo "\n</div>";
        }

        $this->displayFooter();
    }

    /**
     * Load different layout depending on Joomla 2.5 vs 3.x
     * For default layout, the j2 version is not required.
     *
     * @TODO: Test for existence of j2 non-default layout
     *
     * @return string
     */
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
     * @param bool   $tabbed
     *
     * @return string
     */
    protected function renderFieldset($fieldSet, array $sameLine = array(), $tabbed = false)
    {
        $html = array();
        if (!empty($this->form) && $this->form instanceof JForm) {
            $fieldSets = $this->form->getFieldsets();

            if (!empty($fieldSets[$fieldSet])) {
                $name  = $fieldSets[$fieldSet]->name;
                $label = $fieldSets[$fieldSet]->label;

                if (version_compare(JVERSION, '3.0', 'lt')) {
                    $html = $this->renderFieldsetJ2($name, $label, $sameLine, $tabbed);
                } else {
                    $html = $this->renderFieldsetJ3($name, $label, $sameLine, $tabbed);
                }
            }
        }
        return join("\n", $html);
    }

    protected function renderFieldsetJ3($name, $label, array $sameLine = array(), $tabbed = false)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('bootstrap.addTab', 'myTab', $name, JText::_($label));
        }
        $html[] = '<div class="row-fluid">';
        $html[] = '<fieldset class="adminform">';

        foreach ($this->form->getFieldset($name) as $field) {
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            $fieldHtml = array(
                '<div class="control-group">',
                '<div class="control-label">',
                $field->label,
                '</div>',
                '<div class="controls">',
                $field->input
            );
            $html      = array_merge($html, $fieldHtml);

            if (isset($sameLine[$field->fieldname])) {
                $html[] = ' ' . $this->form->getField($sameLine[$field->fieldname])->input;
            }

            $html[] = '</div>';
            $html[] = '</div>';
        }
        $html[] = '</fieldset>';
        $html[] = '</div>';
        if ($tabbed) {
            $html[] = JHtml::_('bootstrap.endTab');
        }

        return $html;
    }

    protected function renderFieldsetJ2($name, $label, array $sameLine = array(), $tabbed = false)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('tabs.panel', JText::_($label), $name . '-page');
        }
        $html[] = '<div class="width-100">';
        $html[] = '<fieldset class="adminform">';
        $html[] = '<ul class="adminformlist">';

        foreach ($this->form->getFieldset($name) as $field) {
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            $fieldHtml = array(
                '<li>' . $field->label . $field->input . '</li>'
            );
            $html      = array_merge($html, $fieldHtml);

            if (isset($sameLine[$field->fieldname])) {
                $html[] = ' ' . $this->form->getField($sameLine[$field->fieldname])->input;
            }

        }
        $endHtml = array(
            '</ul>',
            '</fieldset>',
            '</div>',
            '<div class="clr"></div>'
        );

        return array_merge($html, $endHtml);
    }

    /**
     * Display a header on admin pages
     *
     * @return void
     */
    protected function displayHeader()
    {
        // To be set in subclasses
    }

    /**
     * Display a standard footer on all admin pages
     *
     * @return void
     */
    protected function displayFooter()
    {
        // To be set in subclassess
    }
}
