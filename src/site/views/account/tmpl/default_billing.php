<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if ($this->billing):
    $payment = $this->billing->payment;

    ?>
    <h3><?php echo JText::_('COM_SIMPLERENEW_HEADING_BILLING'); ?></h3>

    <div class="ost-section ost-row-one">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->billing->firstname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section ost-row-two m-bottom b-bottom">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->billing->lastname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <?php
    switch ($this->billing->paymentType) {
        case 'CreditCard':
            ?>

            <h3><?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

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

            <div class="ost-section ost-row-one m-bottom b-bottom">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
                </div>
                <div class="block9">
                    <?php echo JHtml::_('creditcard.expiration', $payment->month, $payment->year); ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <?php
            break;

        case 'PayPal':
            ?>

            <h3><?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

            <div class="ost-alert-warning">
                Paypal is not supported yet
            </div>

            <?php
            break;

        default:
            ?>

            <h3><?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

            <div class="ost-alert-warning">
                <?php echo JText::_('COM_SIMPLERENEW_ERROR_PAYMENT_TYPE_UNKNOWN'); ?>
            </div>

            <?php
            break;

    } // endswitch
else:
    ?>

    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_NO_BILLING_INFO'); ?>
    </div>

<?php
endif;

