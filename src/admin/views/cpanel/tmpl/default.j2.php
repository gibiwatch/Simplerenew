<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

FOFTemplateUtils::addCSS('media://com_simplerenew/css/backend.css');

try {
    // Create a new user - should throw error if already exists
    //$user = User::create('guest@billtomczak.com', 'fred', 'pooka', 'Fred', 'Flintstone');

    // Load current user - Should throw error if not logged in
    //$user = User::getUser();

    // Load a nonexistent user ID - should throw error
    //$user = User::getUser(999999);

    // Load an existing user - Should throw error if User ID does not exist
    $user = User::getUser(463);

    echo join('<br/>', array(
            $user->id,
            $user->fullname,
            $user->firstname,
            $user->lastname,
            $user->username,
            join(':', $user->groups)
        )
    );

    echo '<pre>';
    print_r($user);
    echo '</pre>';

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
