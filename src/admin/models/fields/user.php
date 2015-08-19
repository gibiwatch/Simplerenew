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

class SimplerenewFormFieldUser extends JFormField
{
    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        // Initialize variables.
        $html = array();
        $link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id;

        // Initialize some field attributes.
        $attr = $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
        $attr .= $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

        // Load js support libraries
        JHtml::_('behavior.modal', 'a.modal_' . $this->id);
        JHtml::_('sr.jquery');

        $attribs = SimplerenewUtilitiesArray::toString(
            array(
                'type'     => 'text',
                'readonly' => 'readonly',
                'class'    => 'readonly',
                'id'       => $this->id . '_id',
                'name'     => $this->name,
                'value'    => $this->value
            )
        );
        $html[]  = "<input {$attribs}  />";

        // Create the user select button.
        $noChange = (string)$this->element['nochange'];
        $noChange = ($noChange == 'true' || $noChange == '1');
        if (!$noChange || !$this->value) {
            $html[]  = '<div class="button2-left btn btn-small">';
            $html[]  = '  <div class="blank">';

            $attribs = SimplerenewUtilitiesArray::toString(
                array(
                    'class' => 'modal_' . $this->id,
                    'title' => JText::_('JLIB_FORM_CHANGE_USER'),
                    'href'  => $link,
                    'rel'   => "{handler: 'iframe', size: {x: 800, y: 500}}"
                )
            );
            $html[] = "<a {$attribs} >" . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';

            $html[] = '  </div>';
            $html[] = '</div>';

            $this->addJS();
        }

        return implode("\n", $html);
    }

    /**
     * Additional JS for the modal form button
     */
    protected function addJS()
    {
        // Initialize JavaScript field attributes.
        $onchange = (string)$this->element['onchange'];

        $script = <<<JSCODE
function closeModal() {
    if (SqueezeBox) {
        SqueezeBox.close();
    } else {
        jModalClose();
    }
}
function jSelectUser_{$this->id} (id, title) {
    var old_id = document.getElementById('{$this->id}_id').value;

    if (old_id == id) {
        closeModal();
    } else {
        jQuery.getJSON(
            'index.php',
            {
                option : 'com_simplerenew',
                task   : 'request.user',
                format : 'json',
                id     : id
            },
            function (data, status) {
                var fields = jQuery(':input[id^={$this->formControl}_{$this->group}_]');
                fields.each(function(idx, field) {
                    var name = jQuery(field)
                        .attr('name')
                        .replace(/^.*?\[([^\]]*)\]$/, function(match, $1) {return $1; });
                    if (data[name]) {
                        jQuery(field).val(data[name]);
                    }
                });

                document.getElementById('{$this->id}_id').value = id;
                {$onchange}
                closeModal();
            }
        );
    }
}
JSCODE;

        // Add the script to the document head.
        SimplerenewFactory::getDocument()->addScriptDeclaration($script);
    }
}
