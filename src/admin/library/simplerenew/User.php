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
     * @var User\UserInterface
     */
    protected $adapter = null;

    public function __construct(User\UserInterface $adapter)
    {
        $this->adapter = $adapter;
    }

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
        $this->adapter->load($id);
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
        $this->adapter->loadByUsername($username);
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
        return $this->adapter->get($name);
    }

    public function __set($name, $value)
    {
        return $this->adapter->set($name, $value);
    }

    public function __toString()
    {
        return get_class($this->adapter);
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
        return $this->adapter->get($name);
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
        return $this->adapter->set($name, $value);
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

        foreach ($properties as $k => $v) {
            $this->adapter->set($k, $v);
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
