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
            ->from('#__viewlevels')
            ->order('title ASC');

        $levels = $db->setQuery($query)->loadObjectList('id');

        $keys = array_unique(
            array_merge(
                array_keys((array)$this->value),
                array_keys($levels)
            )
        );

        $values = array();
        foreach ($keys as $key) {
            if (isset($levels[$key])) {
                $values[] = (object)array(
                    'id'    => $key,
                    'label' => $levels[$key]->title,
                    'value' => $this->value[$key]
                );
            }
        }

        $html = array(
            '<div class="clr"></div>',
            sprintf('<ul id="%s" class="simplerenew-sortable">', $this->id)
        );

        foreach ($values as $value) {
            $attribs = array(
                'type'  => 'text',
                'id'    => $this->id . '_' . $value->id,
                'name'  => sprintf('%s[%s]', $this->name, $value->id),
                'value' => $value->value
            );

            $html[] = '<li>'
                . sprintf('<label for="%s">%s</label>', $attribs['id'], $value->label)
                . '<input ' . SimplerenewUtilitiesArray::toString($attribs) . '/>'
                . '</li>';
        }

        $html[] = ' </ul > ';

        $this->loadAssets();

        return join('', $html);
    }

    protected function loadAssets()
    {
        JHtml::_('sr.jquery');
        JHtml::_('script', 'com_simplerenew/jquery-ui.js', false, true);

        $css = <<<STYLES
.simplerenew-sortable label,
.simplerenew-sortable input {
    float: none !important;
    display: inline-block !important;
}
STYLES;

        $js = <<<JSCRIPT
(function($) {
    $(document).ready(function () {
        $('#{$this->id}').sortable();
        $('#{$this->id} li label').css('cursor', 'move');

    });
})(jQuery);
JSCRIPT;

        SimplerenewFactory::getDocument()
            ->addStyleSheet('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css')
            ->addStyleDeclaration($css)
            ->addScriptDeclaration($js);
    }
}
