<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla\User;

use JAuthentication;
use JFactory;
use JLoader;
use Joomla\Registry\Registry;
use JUser;
use JUserHelper;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;
use Simplerenew\User\UserInterface;
use SimplerenewFactory;
use SimplerenewModel;
use UsersModelRegistration;

defined('_JEXEC') or die();

JLoader::register('JAuthentication', JPATH_LIBRARIES . '/joomla/user/authentication.php');


class UserAdapter implements UserInterface
{
    protected $fieldMap = array(
        'password'  => null,
        'password2' => null,
        'firstname' => null,
        'lastname'  => null,
        'enabled'   => 'block'
    );

    /**
     * @var Registry
     */
    protected $userParams = null;

    /**
     * @var array
     */
    protected $localPlans = null;

    /**
     * @var array
     */
    protected $subscriberGroups = null;

    public function __construct()
    {
        SimplerenewModel::addIncludePath(JPATH_SITE . '/components/com_users/models');

        $lang = JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE);

        $this->userParams = \SimplerenewComponentHelper::getParams('com_users');
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws NotFound
     */
    public function loadByUsername(User $parent)
    {
        $username = $parent->username;
        $parent->clearProperties();

        if ($id = JUserHelper::getUserId($username)) {
            $parent->id = $id;
            $this->load($parent);
            return;
        }

        throw new NotFound('Username not found - ' . $username);
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws NotFound
     */
    public function loadByEmail(User $parent)
    {
        $email = $parent->email;
        $parent->clearProperties();

        $db    = SimplerenewFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from('#__users')
            ->where('email = ' . $db->quote($email));

        if ($id = $db->setQuery($query)->loadResult()) {
            $parent->id = $id;
            $this->load($parent);
            return;
        }

        throw new NotFound('Email not found - ' . $email);
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws NotFound
     */
    public function load(User $parent)
    {
        $id = $parent->id;
        $parent->clearProperties();

        // Always load from DB to avoid session conflicts
        if ($id <= 0) {
            $session = SimplerenewFactory::getSession()->get('user');
            $id      = $session->get('id');
        }
        $user = new JUser($id);

        if (!$user || $user->id <= 0) {
            throw new NotFound('User ID not found - ' . (int)$id);
        }

        $keys = array_keys(get_object_vars($parent));
        $data = array_merge(
            $parent->map($user, $keys, $this->fieldMap),
            $this->getName($user->name)
        );

        $data['enabled'] = !$user->block;
        $parent->setProperties($data);
    }

    /**
     * Parse and return a string as a first/last name string
     *
     * @param string $name
     *
     * @return array
     */
    protected function getName($name)
    {
        $name = preg_split('/\s/', $name);

        if (count($name) == 1) {
            $firstname = $name[0];
            $lastname  = '';
        } elseif (count($name) > 1) {
            $lastname  = array_pop($name);
            $firstname = join(' ', $name);
        } else {
            $firstname = '';
            $lastname  = '';
        }

        return array(
            'firstname' => $firstname,
            'lastname'  => $lastname
        );
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function create(User $parent)
    {
        /** @var UsersModelRegistration $model */
        $model = SimplerenewModel::getInstance('Registration', 'UsersModel');

        $data = array(
            'email1'    => $parent->email,
            'username'  => $parent->username,
            'name'      => trim($parent->firstname . ' ' . $parent->lastname),
            'password1' => $parent->password
        );

        // Sometimes Joomla throws an error despite the user being successfully created
        // I really don't like having to do things like this :(
        try {
            $model->register($data);
            $errors = $model->getErrors();

            $parent->loadByUsername($parent->username);

        } catch (Exception $e) {
            $message = $errors ?: array($e->getMessage());
            throw new Exception(join('<br/>', $message), $e->getCode(), $e);
        }
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function update(User $parent)
    {
        $user = new JUser($parent->id);
        if ($user->id != $parent->id) {
            throw new Exception('Unable to update user ID #' . ($parent->id ? $parent->id : 'NULL'));
        }

        $data = array(
            'name'     => trim($parent->firstname . ' ' . $parent->lastname),
            'email'    => $parent->email,
            'username' => $parent->username,
            'groups'   => $parent->groups,
            'block'    => !$parent->enabled
        );

        if (!empty($parent->password)) {
            $data['password']  = $parent->password;
            $data['password2'] = $parent->password2;
        }

        if (!$user->bind($data)) {
            throw new Exception(join('<br/>', array_filter($user->getErrors())), 403);
        }

        if (!$user->save(true)) {
            $errors = $user->getErrors();
            if (!$this->isSuperError($errors)) {
                // Only throw an error if this isn't the 'change SU' problem
                throw new Exception(join('<br/>', array_filter($errors)), 403);
            }
        }

        $this->load($parent);

        // If current user, refresh the session data
        if ($user->id == SimplerenewFactory::getUser()->id) {
            $session = SimplerenewFactory::getSession();
            $session->set('user', $user);
            SimplerenewFactory::getUser();
        }
    }

    /**
     * Validate the password for the user
     *
     * @param User   $parent
     * @param string $password
     *
     * @return bool
     */
    public function validate(User $parent, $password)
    {
        if ($parent->id) {
            // We are not currently supporting 2-factor authentication
            $credentials  = array(
                'username' => $parent->username,
                'password' => $password,
            );
            $authenticate = JAuthentication::getInstance();
            $response     = $authenticate->authenticate($credentials);

            return ($response->status == JAuthentication::STATUS_SUCCESS);
        }
    }

    /**
     * Log the user in
     *
     * @param User   $parent
     * @param string $password
     * @param bool   $force
     *
     * @return void
     * @throws Exception
     */
    public function login(User $parent, $password, $force = false)
    {
        if (empty($parent->username)) {
            throw new Exception('No user selected to login');
        }

        $app = SimplerenewFactory::getApplication();

        $credentials = array(
            'username' => $parent->username,
            'password' => $password
        );

        try {
            $currentUser = clone $parent;
            $currentUser->load();

            if ($currentUser->username == $credentials['username']) {
                // Already logged in
                return;
            } else {
                $this->logout($currentUser);
            }

        } catch (NotFound $e) {
            // Not logged in
        }

        if ($force) {
            $user = new JUser($parent->id);
            if ($user->id != $parent->id) {
                throw new Exception('Unable to login user - ' . $parent->username);
            }

            if ($user->activation || $user->block) {
                $data = array(
                    'activation' => '',
                    'block'      => 0
                );
                $user->bind($data);
                if (!$user->save(true)) {
                    throw new Exception('Unable to activate or unblock user for logging in - ' . $user->username);
                }

                // Update the parent setting
                $parent->enabled = true;
            }
        }

        $response    = $app->login($credentials);
        $currentUser = SimplerenewFactory::getUser();

        if ($response !== false) {
            /*
             * Since Joomla won't tell us that login failed, we have to
             * check for ourselves
             */
            if ($currentUser->username == $credentials['username']) {
                return;
            }
        }

        if ($messages = $app->getMessageQueue()) {
            $error = array_pop($messages);
            $error = $error['message'];

            $session = SimplerenewFactory::getSession();
            $session->set('application.queue', $messages);
        } else {
            $error = 'Unknown error logging in ' . $credentials['username'];
        }

        throw new Exception($error);
    }

    /**
     * Get the list of plans stored locally and containing non-gateway
     * data like user group assignments.
     *
     * @return array
     */
    protected function getLocalPlans()
    {
        if ($this->localPlans === null) {
            $db    = \SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('p.code, p.name, p.group_id, g.title group_name')
                ->from('#__simplerenew_plans p')
                ->innerJoin('#__usergroups g on g.id = p.group_id');

            $this->localPlans = $db->setQuery($query)->loadObjectList('code');
        }
        return $this->localPlans;
    }

    /**
     * Get a list of groups assigned to plans
     *
     * @return array
     */
    protected function getSubscriberGroups()
    {
        if ($this->subscriberGroups === null) {
            $db     = \SimplerenewFactory::getDbo();
            $query  = $db->getQuery(true)
                ->select('g.id, g.title')
                ->from('#__simplerenew_plans p')
                ->innerJoin('#__usergroups g on g.id = p.group_id')
                ->group('g.id, g.title');
            $groups = $db->setQuery($query)->loadObjectList();

            $this->subscriberGroups = array();
            foreach ($groups as $group) {
                $this->subscriberGroups[$group->id] = $group->title;
            }
        }

        return $this->subscriberGroups;
    }

    /**
     * Add user groups based on plans
     *
     * @param User  $parent
     * @param array $planCodes
     * @param bool  $replace Clear all current plan groups
     *
     * @return void
     * @throws Exception
     */
    public function addGroups(User $parent, array $planCodes, $replace = false)
    {
        // Let's make sure we have current data
        $this->load($parent);

        $localPlans = $this->getLocalPlans();
        $expireId   = $parent->getConfig('user.group.expiration');
        $defaultId  = $this->userParams->get('new_usertype');

        $newGroups = array_values(
            array_diff(
                $parent->groups,
                array($expireId, $defaultId)
            )
        );
        if ($replace) {
            $newGroups = $this->filterPlanGroups($newGroups);
        }

        foreach ($planCodes as $planCode) {
            if (is_string($planCode) && isset($localPlans[$planCode])) {
                $plan        = $localPlans[$planCode];
                $newGroups[] = $plan->group_id;
            }
        }
        $newGroups = array_unique($newGroups);
        if (count($newGroups) == 0) {
            $newGroups[] = $parent->groups ? $expireId : $defaultId;
        }

        $parent->groups = $newGroups;
        $this->update($parent);
    }

    /**
     * Remove groups from the user's profile based on the selected plans
     *
     * @param User  $parent
     * @param array $planCodes
     *
     * @return void
     * @throws Exception
     */
    public function removeGroups(User $parent, array $planCodes)
    {
        // Let's make sure we have current data
        $this->load($parent);

        $localPlans = $this->getLocalPlans();
        $newGroups  = array_flip(array_values($parent->groups));

        $defaultId = $this->userParams->get('new_usertype');
        if (isset($newGroups[$defaultId])) {
            unset($newGroups[$defaultId]);
        }

        foreach ($planCodes as $planCode) {
            $gid = isset($localPlans[$planCode]) ? $localPlans[$planCode]->group_id : null;
            if ($gid && isset($newGroups[$gid])) {
                unset($newGroups[$gid]);
            }
        }
        $newGroups = array_keys($newGroups);
        if (count($newGroups) == 0) {
            $newGroups[] = $parent->getConfig('user.group.expiration') ?: $defaultId;
        }

        $parent->groups = $newGroups;
        $this->update($parent);
    }

    /**
     * Get a human friendly version of group membership
     *
     * @param User $parent
     *
     * @return string
     */
    public function getGroupText(User $parent)
    {
        $plans  = $this->getLocalPlans();
        $groups = array();
        foreach ($plans as $plan) {
            $groups[$plan->group_id] = $plan->group_name;
        }

        $text = array();
        foreach ($parent->groups as $groupId) {
            if (isset($groups[$groupId])) {
                $text[] = $groups[$groupId];
            }
        }
        sort($text);
        return join(', ', $text);
    }

    /**
     * Log out if possible
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function logout(User $parent)
    {
        if ($parent->id > 0) {
            $app     = SimplerenewFactory::getApplication();
            $options = array('clientid' => 0);

            if (!$app->logout($parent->id, $options)) {
                throw new Exception('Unable to logout from ' . $parent->username);
            }
        }
    }

    /**
     * Remove all groups that are assigned to plans
     *
     * @param array $groups
     *
     * @return array
     */
    protected function filterPlanGroups(array $groups)
    {
        $filter = array();

        // Get all user groups applying to plans
        $plans = $this->getLocalPlans();
        foreach ($plans as $p) {
            $filter[] = $p->group_id;
        }
        $filter = array_unique($filter);

        // Filter them all out
        return array_values(array_diff($groups, $filter));
    }

    /**
     * We want to ignore the error regarding changes to super user
     * by non-super user. This is a terrible way to do it, but
     * it's the only way Joomla gives us
     *
     * @param array|string $errors
     *
     * @return bool
     */
    protected function isSuperError($errors)
    {
        $strings = array(
            \JText::_('JLIB_USER_ERROR_NOT_SUPERADMIN'), // J2.5.x
            'User not Super Administrator' // J3.x
        );

        foreach ((array)$errors as $error) {
            if (in_array($error, $strings)) {
                // If this was the only reported error, return true
                return (count($errors) == 1);
            }
        }
        return false;
    }
}
