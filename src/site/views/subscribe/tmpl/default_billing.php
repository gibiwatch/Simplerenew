<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<ul>
    <li>
        <label for="billing_firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
        <input
            id="billing_firstname"
            name="billing[firstname]"
            type="text"
            value="<?php echo $this->billing->firstname; ?>"
            required="true"/>
    </li>

    <li>
        <label for="billing_lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
        <input
            id="billing_lastname"
            name="billing[lastname]"
            type="text"
            value="<?php echo $this->billing->lastname; ?>"
            required="true"/>
    </li>

    <li>
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
            <input
                id="billing_cc_number"
                name="billing[cc][number]"
                type="text"
                value=""
                required="true"/>
    </li>

    <li>
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_CVV'); ?>
            <input
                id="billing_cc_cvv"
                name="billing[cc][cvv]"
                type="text"
                value=""
                required="true"/>
    </li>

    <li>
        <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
        <?php echo JHtml::_('srselect.ccyear', 'billing[cc][year]', null, $this->billing->year, 'billing_cc_year'); ?>
        <?php echo JHtml::_('srselect.ccmonth', 'billing[cc][month]', null, $this->billing->month, 'billing_cc_month'); ?>
    </li>
</ul>

<div>
    <?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?>
</div>

