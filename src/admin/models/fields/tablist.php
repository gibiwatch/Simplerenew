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

JFormHelper::loadFieldType('List');

class JFormFieldTablist extends JFormFieldList
{
    public function getInput()
    {
        JHtml::_('sr.jquery');

        $show = array();
        foreach ($this->getOptions() as $option) {
            if (!empty($option->show)) {
                $show[$option->value] = $option->show;
            }
        }
        $show = json_encode($show);

        if ($this->element['container']) {
            $container = (string)$this->element['container'];
        } else {
            $container = version_compare(JVERSION, '3.0', 'lt') ? 'li' : '.control-group';
        }

        $js = <<<JSCODE
(function($) {
    $(document).ready(function($) {
        var show = {$show};
        var control = $('#{$this->id}');
        control.on('change', function(evt) {
            $.each(show, function(value, selector) {
                if (control.val() == value) {
                    $(selector).parents('{$container}').show();
                } else {
                    $(selector).parents('{$container}').hide();
                }
            });
        }).trigger('change');
    });
})(jQuery);
JSCODE;
        JFactory::getDocument()->addScriptDeclaration($js);

        return parent::getInput();
    }

    protected function getOptions()
    {
        $options = parent::getOptions();

        foreach ($options as $option) {
            if ($tabber = $this->element->xpath("option[@value='{$option->value}'][@show]")) {
                $option->show = (string)$tabber[0]['show'];
            }
        }

        return $options;
    }
}
