<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive as Payment;

/**
 * @var SimplerenewViewSubscribe $this
 */

defined('_JEXEC') or die();

if ($this->billing->payment instanceof Payment\CreditCard) {
    $creditCard = $this->billing->payment;
} else {
    $creditCard = new Payment\CreditCard();
}

?>
<div id="content_card" class="content-enabled">
    <input type="hidden" name="payment_method" value="cc"/>

    <div class="p-full">
        <div class="ost-section">
            <div class="block6">
                <label for="billing_firstname">
                    <?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?>
                    <span>*</span>
                </label>
                <input
                    id="billing_firstname"
                    name="billing[firstname]"
                    type="text"
                    value="<?php echo $this->escape($this->billing->firstname); ?>"
                    maxlength="50"
                    required="true"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_FIRSTNAME_REQUIRED'); ?>"/>
            </div>
            <div class="block6">
                <label for="billing_lastname">
                    <?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?>
                    <span>*</span>
                </label>
                <input
                    id="billing_lastname"
                    name="billing[lastname]"
                    type="text"
                    value="<?php echo $this->escape($this->billing->lastname); ?>"
                    maxlength="50"
                    required="true"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_LASTNAME_REQUIRED'); ?>"/>
            </div>
        </div>
        <!-- /.ost-section -->

        <?php echo $this->loadTemplate('address'); ?>

        <div class="ost-section">
            <div class="block5">
                <label>
                    <?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?>
                    <span>*</span>
                </label>
                <input
                    <?php
                    if (!$this->billing->payment->exists()) {
                        echo 'required="true"';
                    }
                    ?>
                    id="billing_cc_number"
                    type="text"
                    value=""
                    maxlength="25"
                    placeholder="<?php echo JHtml::_('creditcard.mask', $creditCard->lastFour); ?>"
                    class="check_ccnumber"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_NUMBER_REQUIRED'); ?>"
                    data-msg-ccnumber="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_NUMBER_INVALID'); ?>"/>
            </div>
            <div class="block2">
                <label for="billing_cc_cvv">
                    <?php echo JText::_('COM_SIMPLERENEW_CC_CVV'); ?>
                    <i class="fa fa-question-circle ost-tooltip-icon ost-tooltip">
                        <span>
                            <?php echo JText::_('COM_SIMPLERENEW_CC_CVV_DESCRIPTION'); ?>
                        </span>
                    </i>
                    <span>*</span>
                </label>
                <input
                    <?php
                    if (!$this->billing->payment->exists()) {
                        echo 'required="true"';
                    }
                    ?>
                    id="billing_cc_cvv"
                    type="text"
                    value=""
                    maxlength="5"
                    class="check_cvv small-width"
                    data-ccnumber="#billing_cc_number"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_CVV_REQUIRED'); ?>"
                    data-msg-cvv="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_CVV_INVALID'); ?>"/>
            </div>
            <div class="block5">
                <label>
                    <?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?>
                    <span>*</span>
                </label>
                <?php
                echo JHtml::_(
                    'srselect.ccyear',
                    'billing[cc][year]',
                    array(
                        'class'           => 'check_date small-width',
                        'data-partner'    => '#billing_cc_month',
                        'data-msg-ccdate' => JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_DATE_INVALID')
                    ),
                    $creditCard->year,
                    'billing_cc_year'
                ); ?>
                <?php
                echo JHtml::_(
                    'srselect.ccmonth',
                    'billing[cc][month]',
                    'class="medium-width"',
                    $creditCard->month,
                    'billing_cc_month'
                ); ?>
            </div>
        </div>
        <!-- /.ost-section -->

    </div>
</div>
