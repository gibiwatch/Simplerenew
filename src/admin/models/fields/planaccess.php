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

    protected $pageControl = null;
    protected $pageClass = null;
    protected $groupControls = null;

    protected function getInput()
    {
        JHtml::_('sr.jquery');

        $this->pageControl   = $this->fieldname . '-pagecontrol';
        $this->pageClass     = $this->fieldname . '-pagetype';
        $this->groupControls = $this->fieldname . '-group-control';

        $pages = array(
            'ALL'    => $this->fieldname . '-all',
            'SELECT' => $this->fieldname . '-select'
        );

        if (version_compare(JVERSION, '3', 'lt')) {
            JHtml::_('behavior.tooltip');
        } else {
            JHtml::_('bootstrap.tooltip');

            $pages['GROUP'] = $this->fieldname . '-group';
        }

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

        $html = array(
            JHtml::_('select.genericlist', $pageOptions, $this->pageControl, null, 'value', 'text', $selected)
        );

        $html[] = '<div class="' . $this->pageClass . '" id="' . $pages['ALL'] . '"></div>';
        $html[] = '<div class="' . $this->pageClass . '" id="' . $pages['SELECT'] . '">';
        $html[] = $this->planSelect();
        $html[] = '</div>';

        if (isset($pages['GROUP'])) {
            $html[] = '<div class="' . $this->pageClass . '" id="' . $pages['GROUP'] . '">';
            $html[] = $this->groupPager();
            $html[] = '</div>';
        }

        $this->addJs();

        return implode("\n", $html);
    }

    protected function createCheckbox($groupId, $option)
    {
        $html = array();

        // Initialize some option attributes.
        $group   = isset($this->value[$groupId]) ? $this->value[$groupId] : array();
        $checked = (is_array($group) && in_array($option->value, $group) ? ' checked' : '');

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
            . $disabled
            . '/>';

        $html[] = '<label for="' . $id . '">' . JText::_($option->text) . '</label>';
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

    /**
     * Selected plans page
     *
     * @return string
     */
    protected function planSelect()
    {
        $plans = $this->getOptions();
        $html  = array();

        if (version_compare(JVERSION, '3', 'ge')) {
            $html[] = $this->pageDescription(JText::_('COM_SIMPLERENEW_PLANACCESS_SELECT_DESC'));
        } else {
            $html[] = '<br style="clear: both;"/>';
        }

        $class  = sprintf('class="%s"', empty($this->class) ? 'checkboxes' : 'checkboxes ' . $this->class);
        $html[] = '<fieldset id="" ' . $class . '>';
        $html[] = '<ul>';
        foreach ($plans as $plan) {
            $html[] = $this->createCheckbox('*', $plan);
        }
        $html[] = '</ul>';
        $html[] = '</fieldset>';

        return join("\n", $html);
    }

    /**
     * Selected plans by group page
     *
     * @return string
     */
    protected function groupPager()
    {
        // Initialize tabs
        $html = array(
            '<p>' . $this->pageDescription(JText::_('COM_SIMPLERENEW_PLANACCESS_BYGROUP_DESC')) . '</p>',
            '<div id="planaccess-sliders" class="tabbable tabs-left">',
        );

        $options = array(
            JHtml::_('select.option', '0', JText::_('COM_SIMPLERENEW_OPTION_PLANACCESS_ALL')),
            JHtml::_('select.option', '1', JText::_('COM_SIMPLERENEW_OPTION_PLANACCESS_SELECT'))
        );

        // Build the selectors and controls
        $selectors = array('<ul class="nav nav-tabs">');
        $controls  = array('<div class="tab-content">');
        $class     = sprintf('class="%s"', empty($this->class) ? 'checkboxes' : 'checkboxes ' . $this->class);

        $groups = $this->getGroups();
        $plans  = $this->getOptions();
        foreach ($groups as $group) {
            $groupId   = $group->value;
            $controlId = $this->fieldname . '-group' . $groupId;

            // Initial Active Tab
            $active = "";
            if ($groupId == 0) {
                $active = "active";
            }

            $selectors[] = '<li class="' . $active . '">';
            $selectors[] = '<a href="#' . $controlId . '" data-toggle="tab">';
            $selectors[] = $group->text;
            $selectors[] = '</a>';
            $selectors[] = '</li>';

            $selected   = (int)(empty($this->value[$groupId]) || is_array($this->value[$groupId]));
            $controls[] = '<div class="' . trim('tab-pane ' . $active) . '" id="' . $controlId . '">';
            $controls[] = JHtml::_(
                'select.genericlist',
                $options,
                $controlId . '-select',
                'class="' . $this->groupControls . '"',
                'value',
                'text',
                $selected
            );
            $controls[] = '<fieldset class="allplans">';
            $controls[] = '<input type="hidden" name="' . $this->name . '[' . $groupId . ']" value="*"/>';
            $controls[] = '</fieldset>';

            $controls[] = '<fieldset ' . $class . '>';
            $controls[] = '<ul>';
            foreach ($plans as $plan) {
                $controls[] = $this->createCheckbox($groupId, $plan);
            }
            $controls[] = '</ul>';
            $controls[] = '</fieldset>';
            $controls[] = '</div>';
        }
        $selectors[] = '</ul>';
        $controls[]  = '</div>';

        $html   = array_merge($html, $selectors, $controls);
        $html[] = '</div>';

        return join("\n", $html);
    }

    protected function pageDescription($text)
    {
        return '<p style="padding: 10px">' . $text . '</p>';
    }

    protected function addJs()
    {
        $js = <<<JS
(function($) {
        $(document).ready(function() {

            var pageControl = $('#{$this->pageControl}');
            var groupControls = $('.{$this->groupControls}');

            groupControls.on('change', function(evt) {
                var selected = ($(this).val() == 1 ? 'checkboxes' : 'allplans');
                $(this).siblings('fieldset').each(function(index, el) {
                    if ($(el).hasClass(selected)) {
                        $(el).find(':input').attr('disabled', false);
                    } else {
                        $(el).find(':input').attr('disabled', true);
                    }
                });
            });

            pageControl.on('change', function(evt) {
                var active = $(this).val();
                $('.{$this->pageClass}').each(function(index, el) {
                    if ($(el).attr('id') == active) {
                        $(el).show();
                        $(el).find(':input').attr('disabled', false);
                        $(el).find('.{$this->groupControls}').trigger('change');
                    } else {
                        $(el).hide().find(':input').attr('disabled', true);
                    }
                });
            }).trigger('change');
        });
})(jQuery);
JS;
        SimplerenewFactory::getDocument()->addScriptDeclaration($js);
    }
}
