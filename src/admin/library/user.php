<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

/**
 * Class User
 *
 * @package Simplerenew
 *
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $fullname
 * @property array  $groups
 */
class User
{
    /**
     * @var User\UserAdapter
     */
    protected $adapter = null;

    /**
     * Load a user from system ID
     *
     * @param int $id
     *
     * @return User
     * @throws Exception
     */
    public function load($id = null)
    {
        $this->getAdapter()->load($id);
        return $this;
    }

    /**
     * Load a user from their username
     *
     * @param $username
     *
     * @return User
     * @throws Exception
     */
    public function loadByUsername($username)
    {
        $this->getAdapter()->loadByUsername($username);
        return $this;
    }

    /**
     * Get the currently set User Adapter
     *
     * @return User\UserAdapter
     */
    public function getAdapter()
    {
        if ($this->adapter === null) {
            $this->setAdapter();
        }
        return $this->adapter;
    }

    /**
     * @param User\UserAdapter $adapter
     *
     * @return User
     * @throws Exception
     */
    public function setAdapter(User\UserAdapter $adapter = null)
    {
        if ($adapter instanceof User\UserAdapter) {
            $this->adapter = $adapter;
        } else {
            if (class_exists('\\JUser')) {
                $className = 'Joomla';
            } else {
                $className = 'Unknown';
            }

            $className = '\\Simplerenew\\User\\' . $className;
            if (!class_exists($className)) {
                throw new Exception('User adapter not found - ' . $className);
            }
            $this->adapter = new $className();
        }
        return $this;
    }

    /**
     * Expose user properties as local properties
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAdapter()->get($name);
    }

    public function __set($name, $value)
    {
        return $this->getAdapter()->set($name, $value);
    }

    public function __toString()
    {
        return get_class($this->getAdapter());
    }

    /**
     * Get a user property
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->getAdapter()->get($name);
    }

    /**
     * Set a user property
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set($name, $value = null)
    {
        return $this->getAdapter()->set($name, $value);
    }

    /**
     * Set multiple user properties
     *
     * @param array|object $data
     *
     * @return void
     * @throws Exception
     *
     */
    public function setProperties($data)
    {
        if (is_object($data)) {
            $properties = get_object_vars($data);
        } elseif (is_array($data)) {
            $properties = $data;
        } else {
            throw new Exception('Expecting object or array. Received ' . gettype($data) . '.');
        }

        $adapter = $this->getAdapter();
        foreach ($properties as $k => $v) {
            $adapter->set($k, $v);
        }
    }

    /**
     * Create a new user.
     *
     * @return User
     * @throws Exception
     */
    public function create()
    {
        $id = $this->adapter->get('id');
        if (empty($id)) {
            $this->adapter->create();
            return $this;
        }

        throw new Exception('User ID must not be set to create new user');
    }

    /**
     * Update the current user with the current property settings
     *
     * @return User
     * @throws Exception
     */
    public function update()
    {
        $id = $this->adapter->get('id');
        if (!empty($id)) {
            $this->adapter->update();
            return $this;
        }

        throw new Exception('No current user to update.');
    }
}
