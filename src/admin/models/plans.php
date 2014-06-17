<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
            'setup', 'plan.setup',
            'published', 'plan.published',
            'id', 'plan.id',
            'created', 'plan.created'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select('plan.*, editor.name as editor');
        $query->from('#__simplerenew_plans plan');
        $query->leftJoin('#__users editor ON plan.checked_out = editor.id');

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

        $listOrder = $this->getState('list.ordering', 'plan.id');
        $listDir   = $this->getState('list.direction', 'ASC');
        $query->order($listOrder . ' ' . $listDir);

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', null, 'string');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest(
            $this->context . '.filter.published',
            'filter_published',
            null,
            'string'
        );
        $this->setState('filter.published', $published);

        parent::populateState('plan.code', 'ASC');
    }
}
