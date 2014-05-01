<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div id="j-sidebar-container" class="span2">
    <?php echo JHtmlSidebar::render(); ?>
</div>
<div id="j-main-container" class="span12">
    <?php
    try {
        $user = new \Simplerenew\User();

        // Create a new user - should throw error if already exists
        /*
        $user->email = 'guest@billtomczak.com';
        $user->username = 'fred';
        $user->password = 'pooka';
        $user->firstname = 'Fred';
        $user->lastname = 'Flintstone';
        $user->create();
        */

        // Set properties using array/object
        /*
        $user->setProperties(array(
                'email' => 'guest@billtomczak.com',
                'username' => 'fred',
                'password' => 'pooka',
                'firstname' => 'Fred',
                'lastname' => 'Flintstone'
            ));
        $user->update();
        $user->create();
        */

        // Load current user
        $user->load();
        $user->create(); // Should generate error

        // Load a nonexistent user ID - should throw error
        //$user->load(999999);

        // Load an existing user - Should throw error if User ID does not exist
        //$user->loadByUsername('admin');

        // Update the user info
        //$user->firstname = 'Super Duper';
        //$user->lastname  = 'User';
        //$user->password = 'xyzzy';
        //$user->update();

        echo join(
            '<br/>',
            array(
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

    } catch (Simplerenew\Exception $e) {
        echo $e->traceMessage();
    }
    ?>
</div>

