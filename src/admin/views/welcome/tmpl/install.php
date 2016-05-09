<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * This file is used in the installation script so we have to load
 * assets in line as the document head will not be modified at this point
 */
$cssPaths = array(
    JHtml::_('stylesheet', 'com_simplerenew/grid.css', null, true, true),
    JHtml::_('stylesheet', 'com_simplerenew/grid-responsive.css', null, true, true),
    JHtml::_('stylesheet', 'com_simplerenew/admin.css', null, true, true)
);
foreach ($cssPaths as $path) {
    echo '<link rel="stylesheet" href="' . $path . '" type="text/css" />' . "\n";
}
?>

<div class="ost-container ost-container-welcome">

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
    <?php
    echo JText::sprintf(
        'COM_SIMPLERENEW_INSTALL_GOTO_OPTIONS',
        JHtml::_('srlink.options', 'COM_SIMPLERENEW_INSTALL_GOTO_OPTIONS_LINKTEXT')
    );
    ?>
    <!-- /.ost-section -->
</div>
<!-- /.ost-container -->
