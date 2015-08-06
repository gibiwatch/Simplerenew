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

JFormHelper::loadFieldClass('GroupedList');

class SimplerenewFormFieldGroupedplans extends JFormFieldGroupedList
{
    public function getGroups()
    {
        $db    = SimplerenewFactory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                array(
                    $db->quoteName('group.title') . ' AS ' . $db->quote('group'),
                    $db->quoteName('plan.code'),
                    $db->quoteName('plan.name')
                )
            )
            ->from('#__simplerenew_plans AS plan')
            ->innerJoin(
                '#__usergroups AS ' . $db->quoteName('group')
                . ' ON ' . $db->quoteName('group.id') . ' = plan.group_id'
            )
            ->order('group.title, plan.code, plan.name');

        $groups = array();

        $plans = $db->setQuery($query)->loadObjectList();
        foreach ($plans as $plan) {
            if (!isset($groups[$plan->group])) {
                $groups[$plan->group] = array();
            }
            $groups[$plan->group][] = JHtml::_('select.option', $plan->code, $plan->code . ' / ' . $plan->name);
        }

        return array_merge_recursive(parent::getGroups(), $groups);
    }
}
