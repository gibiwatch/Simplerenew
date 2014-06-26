<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewModelSubscribe extends SimplerenewModelSite
{
    public function getPlans()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('plans.*')
            ->from('#__simplerenew_plans plans')
            ->order('code');

        if ($plans = $this->getState('filter.plans')) {
            $plans = array_map(array($db, 'quote'), $plans);
            $query->where('code IN (' . join(',', $plans) . ')');
        }

        $query->where('published = 1');

        $list = $db->setQuery($query)->loadObjectList('code');
        return $list;
    }

    protected function populateState()
    {
        $app    = SimplerenewFactory::getApplication();
        $params = $this->state->get('parameters.menu');

        $plans = $params->get('plans');

        $this->setState('filter.plans', $plans);
    }
}
