<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use Simplerenew\Api\Plan;
use Simplerenew\Exception;
use Simplerenew\User\User;

defined('_JEXEC') or die();

interface UserInterface
{
    /**
     * Load the selected User ID
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(User $parent);

    /**
     * Load a user from the username
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function loadByUsername(User $parent);

    /**
     * Create a new user. It is up to the system instances to perform
     * validation on the properties.
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function create(User $parent);

    /**
     * Update the user.
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function update(User $parent);

    /**
     * Validate the password for the user
     *
     * @param User   $parent
     * @param string $password
     *
     * @return bool
     */
    public function validate(User $parent, $password);

    /**
     * Log the user in
     *
     * @param User   $parent
     * @param string $password
     * @param bool   $force
     *
     * @return void
     * @throws Exception
     */
    public function login(User $parent, $password, $force = false);

    /**
     * Log out the current user if possible
     *
     * @return void
     * @throws Exception
     */
    public function logout();

    /**
     * Set the user's group based on the plan
     *
     * @param User $parent
     * @param Plan $plan
     *
     * @return void
     * @throws Exception
     */
    public function setGroup(User $parent, Plan $plan);

    /**
     * Get a human friendly version of group membership
     *
     * @param User $parent
     *
     * @return string
     */
    public function getGroupText(User $parent);
}
