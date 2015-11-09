<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
        $this->addAssets($sortableID);

        // Build the sortable list
        $attribs = array(
            'id' => $sortableID
        );
        if ($class = (string)$this->element['class']) {
            $attribs['class'] = $class;
        }
        $html = array(
            '<ul ' . SimplerenewUtilitiesArray::toString($attribs) . '>'
        );

        $listItem = '<li class="ui-state-default" data-id="%s">'
            . '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'
            . '%s'
            . '</li>';

        $options = $this->getOptions();
        foreach ($options as $option) {
            $html[] = sprintf($listItem, $option->id, $option->title);
        }
        $html[] = '</ul>';

        // Storage for actual value
        $attribs = array(
            'type'  => 'hidden',
            'name'  => $this->name,
            'id'    => $this->id,
            'value' => $this->value
        );

        $html[] = '<input ' . SimplerenewUtilitiesArray::toString($attribs) . ' />';

        return join("\n", $html);
    }

    /**
     * Gets the user groups assigned to plans in the order currently specified
     *
     * @return array
     */
    public function getOptions()
    {
        $db    = SimplerenewFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__usergroups')
            ->where('id IN (Select group_id From #__simplerenew_plans Group By group_id)')
            ->order('id desc');

        $options = $db->setQuery($query)->loadObjectList();
        $ids     = explode('|', $this->value);

        usort($options, function ($a, $b) use ($ids) {
            $orderA = (int)array_search($a->id, $ids);
            $orderB = (int)array_search($b->id, $ids);

            if ($orderA < $orderB) {
                return -1;
            } elseif ($orderA > $orderB) {
                return 1;
            }
            return 0;
        });
        return $options;
    }

    /**
     * Add all css and js needed to run this field
     *
     * @param string $sortableId
     *
     * @return void
     */
    protected function addAssets($sortableId)
    {
        $css = <<<CSS
#{$sortableId} { list-style-type: none; margin: 0; padding: 0; float: left; }
#{$sortableId} li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#{$sortableId} li:nth-child(2) { padding-left: 40px; }
#{$sortableId} li:nth-child(3) { padding-left: 60px; }
#{$sortableId} li:nth-child(4) { padding-left: 80px; }
#{$sortableId} li:nth-child(5) { padding-left: 100px; }
#{$sortableId} li:nth-child(6) { padding-left: 120px; }
#{$sortableId} li:nth-child(7) { padding-left: 140px; }
#{$sortableId} li:hover {cursor: pointer; }
#{$sortableId} li span { position: absolute; margin-left: -1.3em; }
CSS;

        $js = <<<JSCRIPT
  (function($) {
    $(document).ready(function() {
        $('#{$sortableId}').sortable({
            'update': function(event, ui) {
                var items = ui.item.parent('ul').find('li'),
                    target = $('#{$this->id}'),
                    ids;

                 ids = items
                    .map(
                        function() {
                            return $(this).attr('data-id');
                        })
                    .get();

                target.val(ids.join('|'));
            }
        });
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
