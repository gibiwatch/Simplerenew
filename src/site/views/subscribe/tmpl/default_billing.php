<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<h3><span><?php echo JText::_('COM_SIMPLERENEW_HEADING_STEP3'); ?></span> <?php echo JText::_('COM_SIMPLERENEW_HEADING_BILLING'); ?></h3>

<div class="ost-section">
    <div class="block6">
        <label for="billing_firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
        <input
            id="billing_firstname"
            name="billing[firstname]"
            type="text"
            value="<?php echo $this->billing->firstname; ?>"
            required="true"/>
    </div>
    <div class="block6">
        <label for="billing_lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
        <input
            id="billing_lastname"
            name="billing[lastname]"
            type="text"
            value="<?php echo $this->billing->lastname; ?>"
            required="true"/>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section">
    <div class="block6">
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
        <input
            id="billing_cc_number"
            name="billing[cc][number]"
            type="text"
            value=""
            required="true"/>
    </div>
    <div class="block2">
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_CVV'); ?></label>
        <input
            id="billing_cc_cvv"
            name="billing[cc][cvv]"
            type="text"
            value=""
            required="true"
            class="small-width"/>
    </div>
    <div class="block4">
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
        <?php echo JHtml::_('srselect.ccyear', 'billing[cc][year]', 'class="small-width"', $this->billing->year, 'billing_cc_year'); ?>
        <?php echo JHtml::_('srselect.ccmonth', 'billing[cc][month]', 'class="medium-width"', $this->billing->month, 'billing_cc_month'); ?>
    </div>
</div>
<!-- /.ost-section -->

<div>
    <?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?>
</div>

