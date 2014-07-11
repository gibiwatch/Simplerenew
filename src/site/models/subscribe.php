<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once __DIR__ . '/account.php';

class SimplerenewModelSubscribe extends SimplerenewModelAccount
{
    public function getPlans()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('plans.*')
            ->from('#__simplerenew_plans plans')
            ->order('code');

        if ($plans = $this->getState('filter.plans')) {
            // Select the plans based on the user's user group
            $userId = $this->getState('user.id');
            $user   = SimplerenewFactory::getUser($userId);

            // Start by assuming unsubscribed
            $checkGroups = array(0);
            if ($user->id > 0) {
                // See if the user is in a plan group
                $planGroups = $db
                    ->setQuery('Select group_id from #__simplerenew_plans group by group_id')
                    ->loadColumn();

                $checkGroups = array_intersect($planGroups, $user->groups) ? : $checkGroups;
            }

            $available = array();
            foreach ($checkGroups as $group) {
                if (isset($plans[$group])) {
                    if (is_array($plans[$group])) {
                        $available = array_filter(array_merge($available, $plans[$group]));
                    } elseif ($plans[$group] == '*') {
                        // This group makes all plans selected
                        $available = array();
                        break;
                    }
                }
            }

            if ($available) {
                $available = array_map(array($db, 'quote'), $available);
                $query->where('code IN (' . join(',', $available) . ')');
            } else {
                return array();
            }
        }

        $query->where('published = 1');

        $list = $db->setQuery($query)->loadObjectList('code');
        return $list;
    }

    protected function populateState()
    {
        parent::populateState();

        if ($params = $this->state->get('parameters.menu')) {
            $plans = new JRegistry($params->get('plans'));
            $this->setState('filter.plans', $plans->toArray());
        }
    }
}
