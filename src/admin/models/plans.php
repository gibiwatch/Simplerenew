<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewModelPlans extends SimplerenewModelList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'code', 'plan.code',
            'name', 'plan.name',
            'amount', 'plan.amount',
            'setup_cost', 'plan.setup_cost',
            'published', 'plan.published',
            'group', 'ug.title',
            'ordering', 'plan.ordering',
            'trial', 'plan.trial_length'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select(
            array(
                'plan.*',
                'ug.title usergroup',
                'editor.name as editor'
            )
        );
        $query->from('#__simplerenew_plans plan');
        $query->leftJoin('#__users editor ON plan.checked_out = editor.id');
        $query->leftJoin('#__usergroups ug ON ug.id = plan.group_id');

        if ($search = $this->getState('filter.search')) {
            $search = $db->q('%' . $search . '%');
            $ors    = array(
                'plan.name like ' . $search,
                'plan.code like ' . $search
            );
            $query->where('(' . join(' OR ', $ors) . ')');
        }

        $published = $this->getState('filter.published');
        if ($published != '') {
            $query->where('plan.published = ' . $db->quote($published));
        }

        $group = $this->getState('filter.group');
        if ($group > 0) {
            $query->where('ug.id = ' . $db->quote($group));
        }

        $trial = $this->getState('filter.trial');
        if ($trial != '') {
            $operator = $trial ? '>' : '=';
            $query->where("plan.trial_length {$operator} 0");
        }

        $listOrder = $this->getState('list.ordering', 'plan.code');
        $listDir   = $this->getState('list.direction', 'ASC');
        $query->order($listOrder . ' ' . $listDir);

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
        $this->setState('filter.published', $published);

        $group = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group');
        $this->setState('filter.group', $group);

        $trial = $this->getUserStateFromRequest($this->context . '.filter.trial', 'filter_trial');
        $this->setState('filter.trial', $trial);

        parent::populateState('plan.code', 'ASC');
    }
}
