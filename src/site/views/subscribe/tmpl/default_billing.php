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

JHtml::_('sr.tabs');

$paymentOptions = $this->getParams()->get('basic.paymentOptions');

if ($this->billing->payment instanceof Payment\PayPal) {
    $paypal     = $this->billing->payment;
}

echo $this->stepHeading(JText::_('COM_SIMPLERENEW_HEADING_BILLING'));

if (!empty($paypal)): ?>
    <div class="ost-alert-notify m-bottom">
        <?php echo JText::sprintf('COM_SIMPLERENEW_BILLING_EDIT_PAYPAL', $paypal->agreementId); ?>
    </div>
    <?php
endif; ?>

<div class="ost-section payment-tabs">
    <?php
    if (in_array('pp', $paymentOptions)):
        ?>
        <div class="block3 tab-disabled payment-tab" id="tab_paypal" data-content="#content_paypal">
            <h4><i class="fa fa-paypal"></i> <?php echo JText::_('COM_SIMPLERENEW_PAYPAL'); ?></h4>
        </div>
    <?php
    endif;
    ?>

    <?php
    if (in_array('cc', $paymentOptions)):
        ?>
        <div class="block3 tab-disabled payment-tab" id="tab_card" data-content="#content_card">
            <h4><i class="fa fa-credit-card"></i> <?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?></h4>
        </div>
    <?php
    endif;
    ?>
</div>
<!-- /.ost-section -->

<?php
if (in_array('cc', $paymentOptions)) {
    echo $this->loadTemplate('creditcard');
}

if (in_array('pp', $paymentOptions)):
    ?>
    <div class="ost-section" id="content_paypal">
        <div class="block12">
            <div class="p-full">
                <?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?>
            </div>
        </div>
    </div>
    <!-- /.ost-section -->
<?php
endif;
