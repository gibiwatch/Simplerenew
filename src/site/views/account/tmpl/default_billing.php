<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive as Payment;

defined('_JEXEC') or die();
?>

<?php
if ($this->billing):
    $payment = $this->billing->payment;

    ?>

    <?php
    if ($payment instanceof Payment\CreditCard): ?>
        <h3><span><i class="fa fa-credit-card"></i></span> <?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

        <div class="m-bottom b-bottom ost-table">

            <div class="ost-section ost-row-one">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $this->billing->firstname; ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-section ost-row-two">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $this->billing->lastname; ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-section ost-row-one">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_TYPE'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $payment->type; ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-section ost-row-two">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
                </div>
                <div class="block9">
                    <?php echo JHtml::_('creditcard.mask', $payment->lastFour); ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-section ost-row-one">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
                </div>
                <div class="block9">
                    <?php echo JHtml::_('creditcard.expiration', $payment->month, $payment->year); ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <?php echo $this->loadTemplate('address'); ?>

        </div>
        <!-- .simplerenew-credit-card -->

    <?php
    elseif ($payment instanceof Payment\PayPal): ?>
        <h3><span><i class="fa fa-paypal"></i></span> <?php echo JText::_('COM_SIMPLERENEW_PAYPAL'); ?></h3>

        <div class="simplerenew-paypal-information m-bottom ost-table">

            <div class="ost-section ost-row-one">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $this->billing->firstname; ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-section ost-row-two">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $this->billing->lastname; ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <div class="ost-alert-notify">
                <?php echo JText::sprintf('COM_SIMPLERENEW_PAYPAL_AGREEMENTID', $payment->agreementId); ?>
            </div>
            <!-- /.ost-section -->

        </div>
        <!-- .simplerenew-paypal-information -->

    <?php
    else: ?>
        <h3><span><i class="fa fa-credit-card"></i></span> <?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

        <div class="ost-alert-warning">
            <?php echo JText::_('COM_SIMPLERENEW_ERROR_PAYMENT_TYPE_UNKNOWN'); ?>
        </div>

    <?php
    endif;
elseif ($this->subscriptions):
    ?>

    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_NO_BILLING_INFO'); ?>
    </div>

    <?php
endif;
