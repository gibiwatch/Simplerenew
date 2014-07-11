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
    protected function getInput()
    {
        // Initialize some field attributes.
        $class     = !empty($this->class) ? ' class="checkboxes ' . $this->class . '"' : ' class="checkboxes"';
        $required  = $this->required ? ' required aria-required="true"' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';
        $groups    = $this->getGroups();

        // Including fallback code for HTML5 non supported browsers.
        JHtml::_('jquery.framework');
        JHtml::_('script', 'system/html5fallback.js', false, true);

        // Start the checkbox field output.
        $html = array(
            '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . '>',
            '<ul>'
        );

        foreach ($groups as $groupId => $group) {
            $html[] = '<li>'
                . $group['name']
                . '<ul>';
            foreach ($group['options'] as $i => $option) {
                $html[] = $this->createCheckbox($groupId, $i, $option);
            }
            $html[] = '</ul></li>';
        }

        $html[] = '</ul>';

        // End the checkbox field output.
        $html[] = '</fieldset>';

        return implode($html);
    }

    protected function createCheckbox($groupId, $i, $option)
    {
        $html = array();

        // Initialize some option attributes.
        $group   = isset($this->value[$groupId]) ? $this->value[$groupId] : array();
        $checked = (in_array($option->value, $group) ? ' checked' : '');

        $class    = !empty($option->class) ? ' class="' . $option->class . '"' : '';
        $disabled = !empty($option->disable) || $this->disabled ? ' disabled' : '';

        $id     = $this->id . '_' . $groupId . '_' . $i;
        $name   = preg_replace('/\[\]$/', '[' . $groupId . '][]', $this->name);

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
            array(
                'name'    => JText::_('COM_SIMPLERENEW_UNSBUSCRIBED'),
                'options' => array()
            )
        );

        foreach ($options as $option) {
            if (!isset($groups[$option->group_id])) {
                $groups[$option->group_id] = array(
                    'name'    => $option->group,
                    'options' => array()
                );
            }
        }
        foreach ($groups as $group => $list) {
            foreach ($options as $option) {
                $groups[$group]['options'][] = $option;
            }
        }

        return $groups;
    }
}
