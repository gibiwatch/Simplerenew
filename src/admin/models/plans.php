<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewModelPlans extends SimplerenewModellist
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'plan.code',
            'plan.name',
            'plan.amount',
            'plan.setup',
            'plan.published',
            'plan.id'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__simplerenew_plans plan');

        if ($search = $this->getState('filter.search')) {
            $search = $db->q('%' . $search . '%');
            $ors = array(
                'plan.title like ' . $search,
                'plan.code like ' . $search
            );
            $query->where('(' . join(' OR ', $ors) . ')');
        }

        $listOrder = $this->getState('list.ordering');
        $listDir   = $this->getState('list.direction');
        $query->order($listOrder . ' ' . $listDir);

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', null, 'string');
        $this->setState('filter.search', $search);

        parent::populateState('code', 'ASC');
    }
}
