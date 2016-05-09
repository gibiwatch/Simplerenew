<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
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
                JToolbarHelper::divider();
            }
            JToolbarHelper::preferences('com_simplerenew');
        }
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
