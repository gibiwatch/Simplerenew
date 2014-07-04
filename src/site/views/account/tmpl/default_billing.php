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

    <div class="ost-section p-bottom b-bottom">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
            <?php echo $this->billing->firstname; ?>
        </div>
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
            <?php echo $this->billing->lastname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <?php
    switch ($this->billing->paymentType) {
        case 'CreditCard':
            ?>

            <h3><?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h3>

            <div class="ost-section p-bottom b-bottom">
                <div class="block6">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_TYPE'); ?></label>
                    <?php echo $payment->type; ?>
                </div>
                <div class="block2">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
                    <?php echo JHtml::_('creditcard.mask', $payment->lastFour); ?>
                </div>
                <div class="block4">
                    <label><?php echo JText::_('COM_SIMPLERENEW_CC_EXPIRATION'); ?></label>
                    <?php echo JHtml::_('creditcard.expiration', $payment->month, $payment->year); ?>
                </div>
            </div>
            <!-- /.ost-section -->

            <?php
            break;

        case 'PayPal':
            echo 'Paypal is not supported yet';
            break;

        default:
            echo JText::_('COM_SIMPLERENEW_ERROR_PAYMENT_TYPE_UNKNOWN');
            break;

    } // endswitch
else:
    ?>
    <div class="ost-section">
        <div class="block12">
            <?php echo JText::_('COM_SIMPLERENEW_NO_BILLING_INFO'); ?>
        </div>
    </div>
    <!-- /.ost-section -->
<?php
endif;

