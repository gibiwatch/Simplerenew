<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User\Adapter;

use Simplerenew\Exception;
use Simplerenew\User\User;

defined('_JEXEC') or die();

interface UserInterface
{
    /**
     * Load the selected User ID
     *
     * @param int  $id
     * @param User $parent
     *
     * @return UserInterface
     * @throws Exception
     */
    public function load($id, User $parent);

    /**
     * Load a user from the username
     *
     * @param string $username
     * @param User   $parent
     *
     * @return UserInterface
     * @throws Exception
     */
    public function loadByUsername($username, User $parent);

    /**
     * Create a new user. It is up to the system instances to perform
     * validation on the properties.
     *
     * @param User $parent
     *
     * @return UserInterface
     * @throws Exception
     */
    public function create(User $parent);

    /**
     * Update the user.
     *
     * @param User $parent
     *
     * @return UserInterface
     * @throws Exception
     */
    public function update(User $parent);
}
