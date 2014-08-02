<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="ost-container simplerenew-account">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_ACCOUNT_INFO'); ?></h1>
    </div>

    <h3><span><i class="fa fa-info-circle"></i></span> <?php echo JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION'); ?></h3>

    <div class="ost-section ost-row-one">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->user->firstname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section ost-row-two">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->user->lastname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section ost-row-one">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->user->username; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section ost-row-two">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->user->email; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section ost-row-one m-bottom b-bottom">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_MEMBER_GROUP'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->user->groupText ? : JText::_('COM_SIMPLERENEW_MEMBER_GROUP_NONE'); ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <?php echo $this->loadTemplate('billing'); ?>

    <?php echo $this->loadTemplate('subscription'); ?>

</div>
