<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

class SimplerenewFormFieldGrouporder extends JFormField
{
    public function getInput()
    {
        $sortableID = $this->id . '-sortable';

        $class   = (string)$this->element['class'];
        $options = $this->getOptions();

        // Build the sortable list
        $html = array(
            "<ul id=\"{$sortableID}\">"
        );

        $listItem = '<li class="ui-state-default" data-id="%s">'
            . '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'
            . '%s'
            . '</li>';

        foreach ($options as $option) {
            $html[] = sprintf($listItem, $option->id, $option->title);
        }
        $html[] = '</ul>';

        // Storage for actual value
        $attribs = array(
            'type'  => 'hidden',
            'name'  => $this->name,
            'id'    => $this->id,
            'class' => $class
        );
        $html[]  = '<input ' . SimplerenewUtilitiesArray::toString($attribs)  . ' />';

        return join("\n", $html);
    }

    public function getOptions()
    {
        $db    = SimplerenewFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__usergroups')
            ->where('id IN (Select group_id From #__simplerenew_plans Group By group_id)')
            ->order('id desc');

        $options = $db->setQuery($query)->loadObjectList();

        return $options;
    }

    protected function addAssets($id)
    {
        $css = <<<CSS
#{$id} { list-style-type: none; margin: 0; padding: 0; float: left; }
#{$id} li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#{$id} li:hover {cursor: pointer; }
#{$id} li span { position: absolute; margin-left: -1.3em; }
CSS;

        $js = <<<JSCRIPT
  (function($) {
    $(document).ready(function() {
        $('#{$id}').sortable();
        $('#{$id}').disableSelection();
    });
  })(jQuery);
JSCRIPT;

        SimplerenewFactory::getDocument()
            ->addStyleDeclaration($css)
            ->addStyleSheet('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css')
            ->addScriptDeclaration($js);

        JHtml::_('script', 'com_simplerenew/jquery-ui.js', false, true);
    }
}
