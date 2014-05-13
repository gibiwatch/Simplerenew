<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiUser extends RecurlyApibase
{
    protected $classname = 'JUser';

    /**
     * Concatenated string of all member groups this user is in
     *
     * @var string
     */
    protected $memberStatus = null;

    /**
     * The highest member level this user is in
     *
     * @var string
     */
    protected $memberLevel = null;

    public function __construct($id = null)
    {
        // Since we aren't an API supplied by Recurly
        // we need some additional setup work
        if (!is_object($id)) {
            $id = JFactory::getUser($id);
        }
        parent::__construct($id);
    }

    /**
     * Member status is based on Joomla! user group.
     * This will return a comma delimited string of member
     * groups this user is in.
     *
     * @return null|string
     */
    public function getMemberStatus()
    {
        if ($this->memberStatus === null) {
            $planList = new RecurlyApiPlanList();
            $groups   = $planList->userGroups;
            $groupIds = array_intersect(array_keys($groups), $this->groups);

            $status = array();
            foreach ($groupIds as $gid) {
                $status[] = $groups[$gid];
            }
            $this->memberStatus = join(', ', $status);
        }
        return $this->memberStatus;
    }

    public function getMemberLevel()
    {
        if ($this->memberLevel === null) {
            $planList = new RecurlyApiPlanList();
            $levels = $planList->levels;

            foreach ($levels as $level) {
                if (in_array($level->groupId, $this->groups)) {
                    $this->memberLevel = $level->name;
                    break;
                }
            }
        }
        return $this->memberLevel;
    }

    /**
     * Identifies this user as belonging to at least one
     * of the member user groups.
     *
     * @return bool
     */
    public function isMember()
    {
        $planList = new RecurlyApiPlanList();
        $groups   = $planList->userGroups;
        $groupIds = array_intersect(array_keys($groups), $this->groups);
        return (bool)count($groupIds);
    }
}
