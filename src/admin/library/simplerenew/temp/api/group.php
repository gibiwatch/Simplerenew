<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Class RecurlyApiGroup
 *
 * Work with subscription groups
 */
class RecurlyApiGroup
{
    /**
     * @var JUser
     */
    protected $user = null;
    /**
     * @var JUser
     */
    protected $leader = null;
    /**
     * @var array
     */
    protected $members = null;

    /**
     * @param mixed $uid
     *              Either a user ID or a JUser object
     */
    public function __construct($uid = null)
    {
        if ($uid instanceof JUser) {
            $this->user = $uid;
        } else {
            $this->user = JFactory::getUser($uid);
        }
    }

    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        $method = 'get' . ucfirst(strtolower($name));
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * Do not allow any direct setting of properties
     *
     * @param $name
     * @param $value
     *
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR', 401));
    }

    public function getUser()
    {
        return $this->user;
    }
    /**
     * Returns the leader of the group this user is in. Returns null
     * if there is no leader (and thus this user is not part of any group)
     *
     * @return JUser
     */
    public function getLeader()
    {
        if ($this->user && is_null($this->leader)) {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('owner_id');
            $query->from('#__recurly_groups');
            $query->where('owner_id=' . $this->user->id . ' OR member_id=' . $this->user->id);
            $query->group('owner_id');

            $leaderId = $db->setQuery($query)->loadResult();
            if ($leaderId && $leaderId == $this->user->id) {
                $this->leader = $this->user;
            } elseif ($leaderId) {
                $this->leader = JFactory::getUser($leaderId);
            }
        }
        return $this->leader;
    }

    /**
     * Returns all members of the group excluding the group leader. Returns
     * an empty array if there are no group members, and thus this user is not
     * part of any group.
     *
     * @return array
     */
    public function getMembers()
    {
        if (is_null($this->members)) {
            $this->members = array();

            if ($this->user) {
                $leader = $this->getLeader();
                if ($leader) {
                    $db    = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->select('g.member_id');
                    $query->from('#__recurly_groups g');
                    $query->innerJoin('#__users u ON u.id = g.member_id');
                    $query->where('owner_id = ' . $leader->id);

                    $memberIds = $db->setQuery($query)->loadColumn();
                    foreach ($memberIds as $id) {
                        $this->members[$id] = JFactory::getUser($id);
                    }
                }
            }
        }
        return $this->members;
    }

    /**
     * Determine if the passed user ID (or $this->user) is
     * the group leader.
     *
     * @param null $uid
     *
     * @return bool
     */
    public function isLeader($uid = null)
    {
        if ((int)$uid <= 0 && $this->user) {
            $uid = $this->user->id;
        }

        if ((int)$uid <= 0) {
            return false;
        } else {
            $leader = $this->getLeader();
            return $leader && $leader->id == $uid;
        }
    }

    /**
     * Determine if the passed User ID (or $this->user) is
     * a member of the group.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function isMember($uid = null)
    {
        if ((int)$uid <= 0 && $this->user) {
            $uid = $this->user->id;
        }

        if ((int)$uid <= 0) {
            return false;
        } else {
            $members = $this->getMembers();
            return array_key_exists($uid, $members);
        }
    }

    /**
     * Determine if the current user has authorisation to
     * make changes on behalf of the selected user. Or make
     * changes to their own subscription.
     *
     * @param int $memberId
     *
     * @return bool
     */
    public function authorise($memberId = null)
    {
        if ($this->user->id > 0) {
            $db    = JFactory::getDBO();
            $query = $db->getQuery(true);
            if ($memberId && $memberId != $this->user->id) {
                $query->select('id');
                $query->from('#__recurly_groups');
                $query->where(
                    array(
                        'owner_id=' . (int)$this->user->id,
                        'member_id=' . (int)$memberId
                    )
                );
                $result = $db->setQuery($query)->loadResult();
                return (bool)$result;
            } else {
                $userId = $memberId ? $memberId : $this->user->id;

                $query->select('COUNT(*)');
                $query->from('#__recurly_groups');
                $query->where('member_id=' . (int)$userId);
                $cnt = $db->setQuery($query)->loadResult();
                return ($cnt == 0);
            }
        }
    }
}
