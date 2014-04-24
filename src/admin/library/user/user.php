<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

defined('_JEXEC') or die();

/**
 * Class User
 * A wrapper class to abstract system user data
 *
 * @package Simplerenew\User
 *
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $fullname
 * @property array  $groups
 */
abstract class User
{
    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function __construct($id = null)
    {
        $this->user = $this->getSystemObject($id);
    }

    final public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Get the appropriate User object for this system
     *
     * @param null $id
     *
     * @return User
     * @throws \Exception
     */
    final public static function getUser($id = null)
    {
        $class = self::getSystemClassName();
        return new $class($id);
    }

    /**
     *
     * @return string
     * @throws \Exception
     */
    final protected static function getSystemClassName()
    {
        // Only recognize Joomla for now
        if (class_exists('\JUser')) {
            $class = 'Simplerenew\\User\\Joomla';
        }

        if (empty($class) || !class_exists($class)) {
            throw new \Exception(__CLASS__ . ':: No such user class - ' . $class);
        }

        return $class;
    }

    /**
     * Get the value of the user field from a hierarchy of
     * overrides. Subclasses are expected to provide methods
     * or a system object with appropriate properties/methods
     * for retrieving the associated field
     *
     * @param string $name
     *
     * @return mixed
     * @throws \Exception
     */
    final public function get($name)
    {
        $name   = strtolower($name);
        $method = 'get' . ucfirst($name);

        // Allow for override getters
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        // Look to the system object
        if (!empty($this->user->$name)) {
            return $this->user->$name;
        } elseif (method_exists($this->user, $method)) {
            return $this->user->$method();
        } elseif (method_exists($this->user, 'get')) {
            return $this->user->get($name);
        }

        throw new \Exception(__CLASS__ . ':: unknown property - ' . $name);
    }

    /**
     * Get the fullname from its parts
     *
     * @return string
     */
    protected function getFullname()
    {
        return trim($this->get('firstname') . ' ' . $this->get('lastname'));
    }

    /**
     * Public facing method to create a new user
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param array  $groups
     *
     * @return User
     * @throws \Exception
     */
    final public static function create(
        $email,
        $username,
        $password,
        $firstname = null,
        $lastname = null,
        $groups = null
    ) {
        $class = self::getSystemClassName();

        $data = array(
            'email'     => $email,
            'username'  => $username,
            'password'  => $password,
            'firstname' => $firstname . $lastname ? $firstname : $username,
            'lastname'  => $lastname
        );

        return call_user_func($class . '::createUser', $data);
    }

    /**
     * Get the user object for this system.
     * id=0|null should load the current user
     * id>0 loads the selected user
     *
     * @param int $id
     *
     * @return mixed
     * @throws \Exception
     */
    abstract protected function getSystemObject($id = null);

    /**
     * System specific method to create a user.
     *
     * @param $data
     *
     * @return mixed
     */
    abstract public static function createUser($data);
}
