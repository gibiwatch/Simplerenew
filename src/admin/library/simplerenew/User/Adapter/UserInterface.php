<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use Simplerenew\Exception;

defined('_JEXEC') or die();

interface UserInterface
{
    /**
     * Load the selected User ID
     *
     * @param int $id
     *
     * @return UserInterface
     * @throws Exception
     */
    public function load($id = null);

    /**
     * Load a user from the username
     *
     * @param string $username
     *
     * @return UserInterface
     * @throws Exception
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
     * Create a new user with the current user properties.
     * It is up to the system instances to perform validation
     * on the properties.
     *
     * @return UserInterface
     * @throws Exception
     */
    public function create();

    /**
     * Update the system user with the current property settings
     *
     * @return UserInterface
     * @throws Exception
     */
    public function update();
}
