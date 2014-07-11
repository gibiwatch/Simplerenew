<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once __DIR__ . '/plans.php';

class JFormFieldPlanAccess extends JFormFieldPlans
{
    /**
     * @var array
     */
    protected $plans = null;

    protected $forceMultiple = false;

    protected function getInput()
    {
        JHtml::_('bootstrap.tooltip');

        $pages = array(
            'ALL' => $this->fieldname . '-all',
            'SELECT' => $this->fieldname . '-select',
            'GROUP' => $this->fieldname . '-group'
        );

        $pageOptions = array();
        foreach ($pages as $key => $value) {
            $pageOptions[] = JHtml::_(
                'select.option',
                $value,
                JText::_('COM_SIMPLERENEW_OPTION_PLANACCESS_' . $key)
            );
        }

        if (!$this->value) {
            $selected = $pages['ALL'];
        } elseif (isset($this->value['*'])) {
            $selected = $pages['SELECT'];
        } else {
            $selected = $pages['GROUP'];
        }

        $class = $this->fieldname . '-pagetype';
        $html = array(
            JHtml::_('select.genericlist', $pageOptions, $this->fieldname . '-pagecontrol', null, 'value', 'text', $selected),
            '<div class="' . $class . '" id="' . $this->fieldname . '-all"></div>',
            '<div class="' . $class . '" id="' . $this->fieldname . '-select">This page will have just plans</div>',
            '<div class="' . $class . '" id="' . $this->fieldname . '-group">'
        );
        $html = array_merge($html, $this->groupPager());
        $html[] = '</div>';

        $js = <<<JS
(function($) {
        $(document).ready(function() {
            $('#{$this->fieldname}-pagecontrol').on('change', function(evt) {
                var active = $(this).val();
                $('.{$class}').each(function(index, el) {
                    if ($(el).attr('id') == active) {
                        $(el).show();
                        $(el).find(':input').attr('disabled', false);
                    } else {
                        $(el).hide().find(':input').attr('disabled', true);
                    }
                });
            }).trigger('change');
        });
})(jQuery);
JS;
        SimplerenewFactory::getDocument()->addScriptDeclaration($js);

        return implode("\n", $html);
    }

    protected function createCheckbox($groupId, $option)
    {
        $html = array();

        // Initialize some option attributes.
        $group   = isset($this->value[$groupId]) ? $this->value[$groupId] : array();
        $checked = (in_array($option->value, $group) ? ' checked' : '');

        $class    = !empty($option->class) ? ' class="' . $option->class . '"' : '';
        $disabled = !empty($option->disable) || $this->disabled ? ' disabled' : '';

        $id   = $this->id . '_' . $groupId . '_' . $option->value;
        $name = $this->name . '[' . $groupId . '][]';

        $html[] = '<li>';
        $html[] = '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="'
            . htmlspecialchars(
                $option->value,
                ENT_COMPAT,
                'UTF-8'
            ) . '"'
            . $checked
            . $class
            . $disabled
            . '/>';

        $html[] = '<label for="' . $id . '"' . $class . '>' . JText::_($option->text) . '</label>';
        $html[] = '</li>';

        return implode($html);
    }

    protected function getGroups()
    {
        $options = $this->getOptions();

        // Get the field options and recopy into group assignments
        $groups = array(
            (object)array(
                'text'  => JText::_('COM_SIMPLERENEW_UNSBUSCRIBED'),
                'value' => 0
            )
        );

        foreach ($options as $option) {
            $key = (int)$option->group_id;
            if (!isset($groups[$key])) {
                $groups[$key] = (object)array(
                    'text'  => $option->group,
                    'value' => $option->group_id
                );
            }
        }
        return $groups;
    }

    protected function groupPager()
    {
        // Initialize tabs
        $html = array(
            '<p>' . JText::_('COM_SIMPLERENEW_PLANACCESS_DESC') . '</p>',
            '<div id="planaccess-sliders" class="tabbable tabs-left">',
            '<ul class="nav nav-tabs">'
        );

        $groups = $this->getGroups();
        $plans  = $this->getOptions();
        foreach ($groups as $group) {
            // Initial Active Tab
            $active = "";

            if ($group->value == 0) {
                $active = "active";
            }

            $html[] = '<li class="' . $active . '">';
            $html[] = '<a href="#group-' . $group->value . '" data-toggle="tab">';
            $html[] = $group->text;
            $html[] = '</a>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';

        $html[] = '<div class="tab-content">';
        foreach ($groups as $group) {
            // Initial Active Pane
            $active = "";

            if ($group->value == 0) {
                $active = " active";
            }

            $html[] = '<div class="tab-pane' . $active . '" id="group-' . $group->value . '">';
            $html[] = '<fieldset id="" class="checkboxes">';
            $html[] = '<ul>';
            foreach ($plans as $plan) {
                $html[] = $this->createCheckbox($group->value, $plan);
            }
            $html[] = '</ul>';
            $html[] = '</fieldset>';
            $html[] = '</div>';
        }
        $html[] = '</div>';

        $html[] = '</div>';

        return $html;
    }
}