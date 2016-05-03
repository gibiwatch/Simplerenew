<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('User');

class SimplerenewFormFieldUser extends JFormFieldUser
{
    public function getInput()
    {
        $this->addJS();
        return parent::getInput();
    }

    /**
     * Additional JS for the modal form button
     */
    protected function addJS()
    {
        $script = <<<JSCODE
(function ($) {
    $(document).ready(function() {
        $('#{$this->id}_id').on('change', function(evt) {
            $.getJSON(
                'index.php',
                {
                    option : 'com_simplerenew',
                    task   : 'request.user',
                    format : 'json',
                    id     : $(this).val()
                },
                function (data, status) {
                    var fields = $(':input[id^={$this->formControl}_{$this->group}_]');

                    fields.each(function(idx, field) {
                        var name = $(field)
                            .attr('name');
                            
                        if (name) {
                            name = name.replace(/^.*?\[([^\]]*)\]$/, function(match, $1) {return $1; });
                        }
                        if (data[name]) {
                            $(field).val(data[name]);
                        }
                    });
                }
            );
        });
    });
})(jQuery);
JSCODE;

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration($script);
    }
}
