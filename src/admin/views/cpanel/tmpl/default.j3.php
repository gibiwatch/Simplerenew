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
        $adapter = new Simplerenew\Gateway\Recurly\Subscription();
        $account = new Simplerenew\Account($adapter);
        $account->load();

        echo '<pre>';
        print_r($account->getAdapter());
        echo '</pre>';
    } catch (\Simplerenew\Exception $e) {
        echo $e->getTraceMessage();
    }
    ?>
</div>

