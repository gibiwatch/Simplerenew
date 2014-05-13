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
        $account = new Simplerenew\Gateway\Recurly\Account();
        //$account = new Simplerenew\Account();
        //$account->load();

//        $data = file_get_contents(SIMPLERENEW_LIBRARY . '/configuration.json');
//        $config = new Simplerenew\Configuration($data);

        echo '<pre>';
        print_r($account);
        echo '</pre>';
    } catch (\Simplerenew\Exception $e) {
        echo $e->getTraceMessage();
    }
    ?>
</div>

