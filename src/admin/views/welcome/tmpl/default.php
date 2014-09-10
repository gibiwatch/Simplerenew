<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

// Load admin CSS
JHtml::stylesheet('com_simplerenew/grid.css', null, true);
JHtml::stylesheet('com_simplerenew/grid-responsive.css', null, true);
JHtml::stylesheet('com_simplerenew/admin.css', null, true);

?>

<div class="ost-container">

    <div class="ost-section ost-steps">
        <div class="block2">
            <img src="../media/com_simplerenew/images/logo.png" alt="" />
        </div>
        <div class="block1 ost-connector">
            <img src="../media/com_simplerenew/images/points.png" alt="" />
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/ok.png" alt="" />
            <p class="ost-step ost-ok"><?php echo JText::_('Gateway configured'); ?></p>
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/error.png" alt="" />
            <p class="ost-step ost-error"><?php echo JText::_('You do not have plans.
            <br/><a class="btn btn-small" href="#"><span class="icon-plus"></span> Add a new plan</a>'); ?></p>
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/ok.png" alt="" />
            <p class="ost-step ost-ok"><?php echo JText::_('Subscribe view created'); ?></p>
        </div>
    </div>
    <!-- /.ost-section -->
</div>
<!-- /.ost-container -->
