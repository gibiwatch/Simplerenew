<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

defined('_JEXEC') or die();

interface UserAdapter
{
    /**
     * Load the selected User ID
     *
     * @param int $id
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function load($id = null);

    /**
     * Load a user from the username
     *
     * @param string $username
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function loadByUsername($username);

    /**
     * Get a user property
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Set a user property
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed The original value
     */
    public function set($name, $value);

    /**
     * Create a new user.
     *
     * @param array $data associative array passed by the parent User class
     *                    email, username, password, firstname, lastname
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function create(array $data);

    /**
     * Update the system user with the current property settings
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function update();;
}
