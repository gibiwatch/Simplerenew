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

<div class="alert alert-success">
    <?php echo JText::_('COM_SIMPLERENEW_WELCOME'); ?>
</div>

<div class="ost-container">

    <div class="ost-section ost-steps">
        <div class="block2">
            <img src="../media/com_simplerenew/images/logo.png" alt="" />
        </div>
        <div class="block1 ost-connector">
            <img src="../media/com_simplerenew/images/points.png" alt="" />
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/step1.png" alt="" />
            <p class="ost-step"><?php echo JText::_('COM_SIMPLERENEW_WELCOME_OPTIONS'); ?></p>
        </div>
        <div class="block1 ost-connector">
            <img src="../media/com_simplerenew/images/raquo.png" alt="" />
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/step2.png" alt="" />
            <p class="ost-step"><?php echo JText::_('COM_SIMPLERENEW_WELCOME_DETAILS'); ?></p>
        </div>
        <div class="block1 ost-connector">
            <img src="../media/com_simplerenew/images/raquo.png" alt="" />
        </div>
        <div class="block2">
            <img src="../media/com_simplerenew/images/step3.png" alt="" />
            <p class="ost-step"><?php echo JText::_('COM_SIMPLERENEW_WELCOME_SAVE'); ?></p>
        </div>
    </div>
    <!-- /.ost-section -->
</div>
<!-- /.ost-container -->
