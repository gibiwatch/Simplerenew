<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use JAuthentication;
use JFactory;
use JLoader;
use JUser;
use JUserHelper;
use Simplerenew\Api\Plan;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;
use SimplerenewFactory;
use SimplerenewModel;
use UsersModelRegistration;

defined('_JEXEC') or die();

JLoader::register('JAuthentication', JPATH_LIBRARIES . '/joomla/user/authentication.php');


class Joomla implements UserInterface
{
    protected $fieldMap = array(
        'password'  => null,
        'password2' => null,
        'firstname' => null,
        'lastname'  => null,
        'enabled'   => 'block'
    );

    /**
     * @var \JRegistry
     */
    protected $userParams = null;

    /**
     * @var array
     */
    protected $localPlans = null;

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
    public function load(User $parent)
    {
        $id = $parent->id;
        $parent->clearProperties();

        $user = SimplerenewFactory::getUser($id);
        if (!$user || $user->id <= 0) {
            throw new NotFound('User ID not found - ' . (int)$id);
        }

        $keys = array_keys(get_object_vars($parent));
        $data = array_merge(
            $parent->map($user, $keys, $this->fieldMap),
            $this->getName($user->name)
        );

        $data['enabled'] = !$user->block && empty($user->activation);
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

        if ($id = $model->register($data)) {
            $parent->loadByUsername($parent->username);
            return;
        }

        throw new Exception(join('<br/>', $model->getErrors()));
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
            'groups'   => $parent->groups
        );

        if (!empty($parent->password)) {
            $data['password']  = $parent->password;
            $data['password2'] = $parent->password2;
        }

        if (!$user->bind($data)) {
            throw new Exception(join('<br/>', array_filter($user->getErrors())));
        }

        if (!$user->save(true)) {
            throw new Exception(join('<br/>', array_filter($user->getErrors())));
        }

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

        $currentUser = SimplerenewFactory::getUser();
        if ($currentUser->id > 0) {
            if ($currentUser->username == $credentials['username']) {
                // Already logged in
                return;
            } else {
                $this->logout();
            }
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

    protected function getLocalPlans()
    {
        if ($this->localPlans === null) {
            $db               = \SimplerenewFactory::getDbo();
            $query            = $db->getQuery(true)
                ->select('p.code, p.name, p.group_id, g.title group_name')
                ->from('#__simplerenew_plans p')
                ->innerJoin('#__usergroups g on g.id = p.group_id');
            $this->localPlans = $db->setQuery($query)->loadObjectList('code');
        }
        return $this->localPlans;
    }

    /**
     * Set the user's group based on the plan
     *
     * @param User $parent
     * @param Plan $plan
     *
     * @return void
     * @throws Exception
     */
    public function setGroup(User $parent, Plan $plan)
    {
        $plans  = $this->getLocalPlans();
        $filter = array();
        if ($default = $this->userParams->get('new_usertype')) {
            $filter[] = $default;
        }

        foreach ($plans as $p) {
            $filter[] = $p->group_id;
        }
        $filter = array_unique($filter);

        $newGroups = array_diff($parent->groups, $filter);
        if (!isset($this->localPlans[$plan->code])) {
            throw new Exception('Unable to find user group for - ' . $plan->code);
        }

        $gid             = $this->localPlans[$plan->code]->group_id;
        $newGroups[$gid] = $gid;

        $parent->groups = $newGroups;
        $this->update($parent);
    }

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
     * Log out the current user if possible
     *
     * @return void
     * @throws Exception
     */
    public function logout()
    {
        $currentUser = SimplerenewFactory::getUser();
        if ($currentUser->id > 0) {
            if (!SimplerenewFactory::getApplication()->logout()) {
                throw new Exception('Unable to logout from ' . $currentUser->username);
            }
        }
    }
}
