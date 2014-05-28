<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use Simplerenew\Exception;
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
        \JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');

        $lang = \JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE);
    }

    /**
     * @param string $username
     * @param User   $parent
     *
     * @return void
     * @throws Exception
     */
    public function loadByUsername($username, User $parent)
    {
        if ($id = \JUserHelper::getUserId($username)) {
            $this->load($id, $parent);
            return;
        }

        throw new Exception('Username not found - ' . $username);
    }

    /**
     * @param int  $id
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function load($id, User $parent)
    {
        $user = \JFactory::getUser($id);
        if (!$user || $user->id <= 0) {
            throw new Exception('User ID not found - ' . $id);
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
        $model = \JModelLegacy::getInstance('Registration', 'UsersModel');

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

        if ($user->id == $parent->id) {
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
            if ($user->save(true)) {
                // If the current user, refresh the session data
                if ($user->id == \JFactory::getUser()->id) {
                    $session = \JFactory::getSession();
                    $session->set('user', $user);
                    \JFactory::getUser();
                }

                $this->load($parent->id, $parent);
                return;
            }

            throw new Exception('<br/>' . join('<br/>', $user->getErrors()));
        }

        throw new Exception('Unable to update user ID #' . ($parent->id ? $parent->id : 'NULL'));
    }
}
