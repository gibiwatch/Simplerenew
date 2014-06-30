<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class Joomla implements UserInterface
{
    protected $fieldMap = array(
        'password'  => null,
        'firstname' => null,
        'lastname'  => null,
        'enabled'   => 'block'
    );

    public function __construct()
    {
        \SimplerenewModel::addIncludePath(JPATH_SITE . '/components/com_users/models');

        $lang = \JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE);
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function loadByUsername(User $parent)
    {
        $username = $parent->username;
        $parent->clearProperties();

        if ($id = \SimplerenewUserHelper::getUserId($username)) {
            $parent->id = $id;
            $this->load($parent);
            return;
        }

        throw new Exception('Username not found - ' . $username);
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

        $user = \SimplerenewFactory::getUser($id);
        if (!$user || $user->id <= 0) {
            throw new NotFound('User ID not found - ' . $id);
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
        /** @var \UsersModelRegistration $model */
        $model = \SimplerenewModel::getInstance('Registration', 'UsersModel');

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

        throw new Exception('<br/>' . join('<br/>', $model->getErrors()));
    }

    /**
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function update(User $parent)
    {
        $user = new \JUser($parent->id);
        if ($user->id != $parent->id) {
            throw new Exception('Unable to update user ID #' . ($parent->id ? $parent->id : 'NULL'));
        }

        $data = array(
            'name'     => trim($parent->firstname . ' ' . $parent->lastname),
            'email'    => $parent->email,
            'username' => $parent->username
        );

        if (!empty($parent->password)) {
            $data['password']  = $parent->password;
            $data['password2'] = $parent->password;
        }

        $user->bind($data);
        if (!$user->save(true)) {
            throw new Exception('<br/>' . join('<br/>', $user->getErrors()));
        }

        // If current user, refresh the session data
        if ($user->id == \SimplerenewFactory::getUser()->id) {
            $session = \SimplerenewFactory::getSession();
            $session->set('user', $user);
            \SimplerenewFactory::getUser();
        }
    }
}
