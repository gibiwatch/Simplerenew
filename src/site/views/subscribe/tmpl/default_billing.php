<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive as Payment;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 * @var Payment\Paypal           $paypal
 * @var Payment\CreditCard       $creditCard
 */

if ($this->billing->payment instanceof Payment\PayPal) {
    $paypal     = $this->billing->payment;
    $creditCard = new Payment\CreditCard();
} else {
    $creditCard = $this->billing->payment;
}

echo $this->stepHeading(JText::_('COM_SIMPLERENEW_HEADING_BILLING'));

if (!empty($paypal)): ?>
    <div class="ost-alert-notify m-bottom">
        <?php echo JText::sprintf('COM_SIMPLERENEW_BILLING_EDIT_PAYPAL', $paypal->agreementId); ?>
    </div>
    <?php
endif; ?>

<div class="ost-section">
    <div class="block3 tab-disabled" id="tab_paypal">
        <h4><i class="fa fa-paypal"></i> <?php echo JText::_('COM_SIMPLERENEW_PAYPAL'); ?></h4>
    </div>
    <div class="block3 tab-enabled" id="tab_card">
        <h4><i class="fa fa-credit-card"></i> <?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h4>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section content-disabled" id="content_paypal">
    <div class="block12">
        <div class="p-full">
            <?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?>
        </div>
    </div>
</div>
<!-- /.ost-section -->

<div id="content_card" class="content-enabled">
    <div class="p-full">
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

        <?php echo $this->loadTemplate('address'); ?>

        <div class="ost-section">
            <div class="block6">
                <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
                <input
                    <?php
                    if (!$this->billing->payment->exists()) {
                        echo 'required="true"';
                    }
                    ?>
                    id="billing_cc_number"
                    name="billing[cc][number]"
                    type="text"
                    value=""
                    placeholder="<?php echo JHtml::_('creditcard.mask', $creditCard->lastFour); ?>"/>
            </div>
            <div class="block2">
                <label><?php echo JText::_('COM_SIMPLERENEW_CC_CVV'); ?></label>
                <input
                    <?php
                    if (!$this->billing->payment->exists()) {
                        echo 'required="true"';
                    }
                    ?>
                    id="billing_cc_cvv"
                    name="billing[cc][cvv]"
                    type="text"
                    value=""
                    class="small-width"/>
            </div>
            <div class="block4">
                <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
                <?php
                echo JHtml::_(
                    'srselect.ccyear',
                    'billing[cc][year]',
                    'class="small-width"',
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

