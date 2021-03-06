<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

use Simplerenew\Api\Subscription;
use Simplerenew\Configuration;
use Simplerenew\Container;
use Simplerenew\Exception;
use Simplerenew\Object;

defined('_JEXEC') or die();

/**
 * Class User
 *
 * @package Simplerenew
 *
 * @property-read string $fullname
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
     * @var UserInterface
     */
    protected $adapter = null;

    /**
     * @var Configuration
     */
    protected $configuration = null;

    public function __construct(Configuration $config, UserInterface $adapter)
    {
        $this->configuration = $config;
        $this->adapter       = $adapter;
    }

    public function __get($name)
    {
        if ($name == 'fullname') {
            return $this->getFullName();
        }
        return null;
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
    public function loadByUsername($username)
    {
        $this->username = $username;
        $this->adapter->loadByUsername($this);

        return $this;
    }

    /**
     * Load a user from their email address
     *
     * @param $email
     *
     * @return User
     * @throws Exception
     */
    public function loadByEmail($email)
    {
        $this->email = $email;
        $this->adapter->loadByEmail($this);

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
        $this->adapter->logout($this);
    }

    /**
     * Add user groups based on selected plans
     *
     * @param mixed $planCodes Plan code or array of plan codes to add to user
     * @param bool  $replace   Clear all subscriber groups
     *
     * @return User
     * @throws Exception
     */
    public function addGroups($planCodes, $replace = false)
    {
        $this->adapter->addGroups($this, (array)$planCodes, $replace);
        return $this;
    }

    /**
     * Update user group settings based on subscriptions through the selected containers
     *
     * @param array $containers
     *
     * @return User
     */
    public function resetGroups(array $containers = null)
    {
        $plans = array();
        if ($this->id) {
            foreach ($containers as $container) {
                if ($container instanceof Container) {
                    try {
                        $account       = $container->account->load($this);
                        $subscriptions = $container
                            ->subscription
                            ->getList($account, ~Subscription::STATUS_EXPIRED);

                        foreach ($subscriptions as $subscription) {
                            $plans[] = $subscription->plan;
                        }

                    } catch (Exception\NotFound $e) {
                        // Perfectly fine
                    }
                }
            }
        }
        $this->adapter->addGroups($this, $plans, true);

        return $this;
    }

    /**
     * @param mixed $planCodes Plan code or array of plan codes
     *
     * @return User
     * @throws Exception
     */
    public function removeGroups($planCodes)
    {
        $this->adapter->removeGroups($this, (array)$planCodes);
        return $this;
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

    /**
     * Returns the configuration item
     *
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return $this->configuration->get($key, $default);
    }

    /**
     * Wrapper function for easy and consistent retrieval of full name
     *
     * @return string
     */
    public function getFullName()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }
}
