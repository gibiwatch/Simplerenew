<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('Text');

class SimplerenewFormFieldRedirect extends JFormFieldText
{
    protected function getInput()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__usergroups')
            ->order('title ASC');

        $groups = $db->setQuery($query)->loadObjectList('id');

        $keys = array_unique(
            array_merge(
                array_keys((array)$this->value),
                array_keys($groups)
            )
        );

        $values = array();
        foreach ($keys as $key) {
            if (isset($groups[$key])) {
                $values[] = (object)array(
                    'id'    => $key,
                    'label' => $groups[$key]->title,
                    'value' => $this->value[$key]
                );
            }
        }

        $html = array(
            '<div class="clr"></div>',
            sprintf('<ul id="%s" class="simplerenew-sortable">', $this->id)
        );

        $label = '<label for="%s"><i class="fa fa-arrows"></i> %s</label>';
        foreach ($values as $value) {
            $attribs = array(
                'type'  => 'text',
                'id'    => $this->id . '_' . $value->id,
                'name'  => sprintf('%s[%s]', $this->name, $value->id),
                'value' => $value->value
            );

            $html[] = '<li>'
                . sprintf($label, $attribs['id'], $value->label)
                . '<input ' . SimplerenewUtilitiesArray::toString($attribs) . '/>'
                . '</li>';
        }

        $html[] = ' </ul > ';

        $this->loadAssets();

        return join('', $html);
    }

    protected function loadAssets()
    {
        JHtml::_('stylesheet', 'com_simplerenew/awesome/css/font-awesome.min.css', null, true);
        JHtml::_('sr.jquery');
        JHtml::_('script', 'com_simplerenew/jquery-ui.js', false, true);

        SimplerenewFactory::getDocument()
            ->addStyleSheet('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css')
            ->addStyleDeclaration($this->getCss())
            ->addScriptDeclaration($this->getJavascript());
    }

    protected function getCss()
    {
        $css = <<<STYLES
#{$this->id}-lbl {
    font-weight: 600;
    margin-bottom: 15px;
}
#{$this->id} {
    display: table;
}
#{$this->id} > li {
    padding: 5px;
    list-style: none;
}
#{$this->id} > li:nth-child(2n+1) {
     background: rgba(242,242,242,0.5);
}
#{$this->id} > li > label,
#{$this->id} > li > input[type="text"] {
    float: none;
    display: inline-block;
    width: 220px;
    margin: 0;
}
STYLES;
        return $css;
    }

    protected function getJavascript()
    {
        $js = <<<JSCRIPT
(function($) {
    $(document).ready(function () {
        $('#{$this->id}').sortable();
        $('#{$this->id} li label').css('cursor', 'move');

    });
})(jQuery);
JSCRIPT;
        return $js;
    }
}
