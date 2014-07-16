<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var SimplerenewViewSubscribe $this */
?>
<div class="ost-section ost-row-two">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_ADDRESS1'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->address1; ?>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section ost-row-one">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_ADDRESS2'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->address2; ?>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section ost-row-two">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_CITY'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->city; ?>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section ost-row-one">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_REGION'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->region; ?>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section ost-row-two">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_POSTAL'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->postal; ?>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section ost-row-one m-bottom b-bottom">
    <div class="block3">
        <label><?php echo JText::_('COM_SIMPLERENEW_BILLING_COUNTRY'); ?></label>
    </div>
    <div class="block9">
        <?php echo $this->billing->address->country; ?>
    </div>
</div>
<!-- /.ost-section -->
