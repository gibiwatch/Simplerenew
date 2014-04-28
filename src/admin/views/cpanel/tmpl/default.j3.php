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
        //$user->create('guest@billtomczak.com', 'fred', 'pooka', 'Fred', 'Flintstone');

        // Load current user - Should throw error if not logged in
        //$user->load();

        // Load a nonexistent user ID - should throw error
        //$user->load(999999);

        // Load an existing user - Should throw error if User ID does not exist
        $user->load(466);


        // Update the user info
        //$user->firstname = 'Fred';
        //$user->lastname  = 'Flintstone';
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

    } catch (Exception $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    ?>
</div>

