<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiPlanList extends RecurlyApibase
{
    protected $classname = 'Recurly_PlanList';

    protected $cacheId = 'plan.list.get';

    protected $levels = null;
    protected $plans = null;
    protected $map = null;

    /**
     * Caching version of Recurly_PlanList::get()
     * Also returns plans wrapped in RecurlyApiPlan class
     *
     * @return array
     */
    public function get()
    {
        if ($this->plans === null) {
            $cache = $this->getCache();

            $this->plans = $cache->get($this->cacheId);
            if ($this->plans === false) {
                $class = $this->classname;
                $plans = $class::get();

                $this->plans = array();
                foreach ($plans as $plan) {
                    $this->plans[$plan->plan_code] = new RecurlyApiPlan($plan);
                }

                // Sort on plan code
                ksort($this->plans);
                $cache->store($this->plans, $this->cacheId);
            }
        }
        return $this->plans;
    }

    /**
     * This class is always valid
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Force an uncached reload of plans;
     *
     * @param null $id
     *
     * @return array
     */
    public function load($id = null)
    {
        $this->plans = null;
        $cache = $this->getCache();
        $cache->remove($this->cacheId);
        return $this->get();
    }

    /**
     * get level definitions from component parameters
     *
     * @return array
     */
    public function getLevels()
    {
        if ($this->levels === null) {
            $cache = $this->getCache();
            $cacheId = 'plan.list.levels';

            $this->levels = $cache->get($cacheId);
            if ($this->levels === false) {
                $this->levels = array();
                $params       = JComponentHelper::getParams('com_recurly');
                $db           = JFactory::getDbo();

                // Load initial
                $defined = preg_split('/\r?\n/', $params->get('levels'));
                $levels  = array();
                $groups  = array();
                foreach ($defined as $definition) {
                    $delim = $definition[0];
                    $level = explode($delim, substr($definition, 1));
                    if (count($level) == 3) {
                        $levels[] = (object)array(
                            'name'    => $level[0],
                            'regexp' => '/' . $level[1] . '/i',
                            'group'   => $level[2]
                        );
                        $groups[]   = $level[2];
                    }
                }

                $groups = array_map(array($db, 'q'), $groups);
                $query  = $db->getQuery(true);
                $query->select('id,title');
                $query->from('#__usergroups');
                $query->where('title IN (' . join(',', $groups) . ')');
                $groups = $db->setQuery($query)->loadObjectList('title');

                $this->levels = array();
                foreach ($levels as $level) {
                    if (!empty($groups[$level->group])) {
                        $level->groupId = $groups[$level->group]->id;
                        $this->levels[] = $level;
                    }
                }
                $cache->store($this->levels, $cacheId);
            }
        }
        return $this->levels;
    }

    /**
     * Get list of the member user groups
     *
     * @return array
     */
    public function getUserGroups()
    {
        $levels = $this->getLevels();

        $groups = array();
        foreach ($levels as $level) {
            $groups[$level->groupId] = $level->group;
        }
        return $groups;
    }

    /**
     * Get a filtered list of plans based on selection
     *
     * @param mixed $selection
     *              An array of plan codes to select from the available pool
     *              of plans or a comma delimited list of same
     *
     * @return array
     */
    public function getFilteredList($selection)
    {
        $allPlans = $this->get();

        if (is_string($selection)) {
            $selection = explode(',', $selection);
            $selection = array_map('trim', $selection);
        }
        if (empty($selection) || !is_array($selection)) {
            return $allPlans;
        }

        $plans = array();
        foreach ($selection as $code) {
            if (array_key_exists($code, $allPlans)) {
                $plans[$code] = $allPlans[$code];
            }
        }
        return $plans;
    }

    /**
     * Get map of plans to user groups
     *
     * @return array
     */
    public function getMap()
    {
        if ($this->map === null) {
            $levels = $this->getLevels();

            $list      = $this->get();
            $this->map = array();
            foreach ($list as $plan) {
                foreach ($levels as $level) {
                    if (preg_match($level->regexp, $plan->name)) {
                        $this->map[$plan->plan_code] = $level->groupId;
                        break;
                    }
                }
            }
            // Use the default user group for any unmatched plans
            $default = JComponentHelper::getParams('com_users')->get('new_usertype');
            $this->map['*'] = $default;
        }
        return $this->map;
    }
}
