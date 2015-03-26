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

JFormHelper::loadFieldClass('Checkboxes');

class JFormFieldPlans extends JFormFieldCheckboxes
{
    /**
     * @var array
     */
    protected $options = null;

    protected function getOptions()
    {
        if ($this->options === null) {
            $this->options = array();

            // Recognized Field Attributes
            $format = $this->element['format'] ? (string)$this->element['format'] : '%name%';

            $orders    = array(
                'ordering' => 'p.ordering',
                'code'     => 'p.code',
                'name'     => 'p.name',
                'group'    => 'g.title'
            );
            $listOrder = $this->element['order'];
            $listOrder = empty($orders[$listOrder]) ? $orders['ordering'] : $orders[$listOrder];
            $listDir   = $this->element['direction'] ? (string)$this->element['direction'] : 'ASC';

            $db       = SimplerenewFactory::getDbo();
            $fields   = array_map(
                array($db, 'quoteName'),
                array(
                    'p.code',
                    'p.name',
                    'p.amount',
                    'p.currency',
                    'p.trial_length',
                    'p.trial_unit',
                    'p.group_id'
                )
            );
            $fields[] = $db->quoteName('g.title', 'group');
            $fields[] = $db->quote('') . ' ' . $db->quoteName('currency');

            $query = $db->getQuery(true)
                ->select($fields)
                ->from('#__simplerenew_plans p')
                ->innerJoin('#__usergroups g on g.id = p.group_id')
                ->order($listOrder . ' ' . $listDir);

            $localPlans  = $db->setQuery($query)->loadObjectList();
            foreach ($localPlans as $plan) {
                $text = str_replace(
                    array(
                        '{code}',
                        '{name}',
                        '{amount}',
                        '{trial}',
                        '{group}'
                    ),
                    array(
                        $plan->code,
                        $plan->name,
                        JHtml::_('currency.format', $plan->amount, $plan->currency),
                        JHtml::_('plan.trial', $plan->trial_length, $plan->trial_unit),
                        $plan->group
                    ),
                    $format
                );
                $option           = JHtml::_('select.option', $plan->code, $text);
                $option->checked  = false;
                $option->group    = $plan->group;
                $option->group_id = $plan->group_id;
                $this->options[]  = $option;
            }
        }
        return $this->options;
    }
}
