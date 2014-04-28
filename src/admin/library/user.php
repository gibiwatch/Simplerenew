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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \InvalidArgumentException
     */
    public function setAdapter(User\UserAdapter $adapter = null)
    {
        if (!$adapter instanceof User\UserAdapter) {
            if (class_exists('\\JUser')) {
                $className = 'Joomla';
            } else {
                $className = 'Unknown';
            }

            $className = '\\Simplerenew\\User\\' . $className;
            if (!class_exists($className)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        __CLASS__ . ':: User adapter not found - %s',
                        $className
                    )
                );
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
     * Create a new user
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     *
     * @return User
     * @throws \Exception
     */
    final public function create(
        $email,
        $username,
        $password,
        $firstname = null,
        $lastname = null
    ) {

        $data = array(
            'email'     => $email,
            'username'  => $username,
            'password'  => $password,
            'firstname' => $firstname . $lastname ? $firstname : $username,
            'lastname'  => $lastname
        );

        return $this->getAdapter()->create($data);
    }

    /**
     * Update the current user with the current property settings
     *
     * @return User
     * @throws \Exception
     */
    final public function update()
    {
        $this->getAdapter()->update();
        return $this;
    }
}
