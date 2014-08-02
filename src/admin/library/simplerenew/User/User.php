<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

use Simplerenew\Api\Plan;
use Simplerenew\Exception;
use Simplerenew\Object;

defined('_JEXEC') or die();

/**
 * Class User
 *
 * @package Simplerenew
 *
 */
class User extends Object
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $username = null;

    /**
     * @var string
     */
    public $email = null;

    /**
     * @var string
     */
    public $password = null;

    /**
     * @var String
     */
    public $password2 = null;

    /**
     * @var string
     */
    public $firstname = null;

    /**
     * @var string
     */
    public $lastname = null;

    /**
     * @var array
     */
    public $groups = array();

    /**
     * @var bool
     */
    public $enabled = null;

    /**
     * @var Adapter\UserInterface
     */
    protected $adapter = null;

    public function __construct(Adapter\UserInterface $adapter)
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
        $this->clearProperties();
        $this->id = $id;
        $this->adapter->load($this);

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
    public function loadByUsername($username = null)
    {
        $this->username = $username;
        $this->adapter->loadByUsername($this);

        return $this;
    }

    public function asString()
    {
        return get_class($this->adapter);
    }

    /**
     * Create a new user.
     *
     * @return User
     * @throws Exception
     */
    public function create()
    {
        if (empty($this->id)) {
            $this->adapter->create($this);

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
        if (!empty($this->id)) {
            $this->adapter->update($this);

            return $this;
        }

        throw new Exception('No current user to update.');
    }

    /**
     * Check the password for validity
     *
     * @param $password
     *
     * @return bool
     */
    public function validate($password)
    {
        return $this->adapter->validate($this, $password);
    }

    /**
     * Login the user
     *
     * @param string $password
     * @param bool   $force
     *
     * @return void
     * @throws Exception
     */
    public function login($password, $force = false)
    {
        $this->adapter->login($this, $password, $force);
    }

    /**
     * Logout the user
     *
     * @return void
     * @throws Exception
     */
    public function logout()
    {
        $this->adapter->logout();
    }
    /**
     * Set the user group based on the plan
     *
     * @param Plan $plan
     *
     * @return void
     * @throws Exception
     */
    public function setGroup(Plan $plan = null)
    {
        $this->adapter->setGroup($this, $plan);
    }

    /**
     * Get a human friendly version of group membership
     *
     * @return string
     */
    public function getGroupText()
    {
        return $this->adapter->getGroupText($this);
    }
}
