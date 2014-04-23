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
 * @property array  $groups
 *
 */
abstract class User
{
    protected $user = null;

    /**
     * @param int $id
     */
    public function __construct($id = null)
    {
        $this->user = $this->getSystemObject($id);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Determine what system we're running on and get an appropriate user object
     *
     * @param null $id
     *
     * @return User
     * @throws \Exception
     */
    public static function getInstance($id = null)
    {
        // Only on Joomla for now
        $class = 'Simplerenew\\User\\Joomla';
        if (class_exists($class)) {
            return new $class($id);
        }

        throw new \Exception('No such user class ' . $class);
    }

    /**
     * Standard getter
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        $name   = strtolower($name);
        $method = 'get' . ucfirst($name);

        // Allow subclasses to provide their own getters
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        // Then look to the system object
        if (!empty($this->user->$name)) {
            return $this->user->$name;
        } elseif (method_exists($this->user, $method)) {
            return $this->user->$method();
        } elseif (method_exists($this->user, 'get')) {
            return $this->user->get($name);
        }

        return null;
    }

    /**
     * Get the user object for this system.
     * id=-1 should load a blank/empty user object
     * id=0|null should load the current user
     * id>0 loads the selected user
     *
     * @param int $id
     *
     * @return mixed
     */
    abstract protected function getSystemObject($id = null);

    /**
     * Create a new user
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param array  $groups
     *
     * @return User
     */
    abstract public static function create(
        $email,
        $username,
        $password,
        $firstname=null,
        $lastname=null,
        $groups=array());
}
