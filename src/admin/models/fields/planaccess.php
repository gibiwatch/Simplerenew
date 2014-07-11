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

    protected function getInput()
    {
        JHtml::_('bootstrap.tooltip');

        // Initialize tabs
        $html = array(
            '<p class="planaccess-desc">' . 'planaccess-desc' . '</p>',
            '<div id="planaccess-sliders" class="tabbable tabs-left">',
            '<ul class="nav nav-tabs">'
        );

        $groups = $this->getGroups();
        $plans  = $this->getOptions();
        foreach ($groups as $group) {
            // Initial Active Tab
            $active = "";

            if ($group->value == 1) {
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

        // Start a row for each user group.
        foreach ($groups as $group) {
            // Initial Active Pane
            $active = "";

            if ($group->value == 1) {
                $active = " active";
            }

            $html[] = '<div class="tab-pane' . $active . '" id="group-' . $group->value . '">';
            $html[] = '<table class="table table-striped">';
            $html[] = '<thead>';
            $html[] = '<tr>';

            $html[] = '<th class="actions" id="actions-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . 'Plan Name' . '</span>';
            $html[] = '</th>';

            $html[] = '<th class="settings" id="settings-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . 'Setting' . '</span>';
            $html[] = '</th>';

            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';

            foreach ($plans as $plan) {
                $html[] = '<tr>';
                $html[] = '<td headers="actions-th' . $group->value . '">';
                $html[] = '<label for="' . $this->id . '_' . $plan->value . '_' . $group->value . '" class="hasTooltip" title="'
                    . htmlspecialchars(
                        JText::_($plan->text),
                        ENT_COMPAT,
                        'UTF-8'
                    ) . '">';
                $html[] = JText::_($plan->text);
                $html[] = '</label>';
                $html[] = '</td>';

                $html[] = '<td headers="settings-th' . $group->value . '">';

                $html[] = '<select class="input-small" name="' . $this->name . '[' . $plan->value . '][' . $group->value . ']" id="' . $this->id . '_' . $plan->value
                    . '_' . $group->value . '" title="'
                    . JText::sprintf(
                        'JLIB_RULES_SELECT_ALLOW_DENY_GROUP',
                        JText::_($plan->text),
                        trim($group->text)
                    ) . '">';

                $html[] = '</td>';


                $html[] = '</td>';

                $html[] = '</tr>';
            }

            $html[] = '</tbody>';
            $html[] = '</table></div>';
        }

        $html[] = '</div></div>';

        $html[] = '<div class="alert">';

        $html[] = '</div>';

        return implode("\n", $html);
    }

    protected function createCheckbox($groupId, $i, $option)
    {
        $html = array();

        // Initialize some option attributes.
        $group   = isset($this->value[$groupId]) ? $this->value[$groupId] : array();
        $checked = (in_array($option->value, $group) ? ' checked' : '');

        $class    = !empty($option->class) ? ' class="' . $option->class . '"' : '';
        $disabled = !empty($option->disable) || $this->disabled ? ' disabled' : '';

        $id   = $this->id . '_' . $groupId . '_' . $i;
        $name = preg_replace('/\[\]$/', '[' . $groupId . '][]', $this->name);

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

}
