<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive\PayPal;
use Simplerenew\Primitive\CreditCard;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 * @var Paypal                   $paypal
 */

$paymentOptions = $this->getParams()->get('basic.paymentOptions');
$tabOptions = array();

$activeTab = null;
if ($this->billing->payment instanceof PayPal) {
    $paypal = $this->billing->payment;
    $tabOptions['active'] = '#tab_paypal';
} elseif ($this->billing->payment instanceof CreditCard) {
    $tabOptions['active'] = '#tab_card';
}

JHtml::_('sr.validation.billing');
JHtml::_('sr.tabs', '.payment-tabs div', $tabOptions);

echo $this->stepHeading(JText::_('COM_SIMPLERENEW_HEADING_BILLING'));

if (!empty($paypal)):
    ?>
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
            <h4>
                <i class="fa fa-paypal"></i>
                <?php echo JText::_('COM_SIMPLERENEW_PAYPAL'); ?>
            </h4>
        </div>
    <?php
    endif;
    ?>

    <?php
    if (in_array('cc', $paymentOptions)):
        ?>
        <div class="block3 tab-disabled payment-tab" id="tab_card" data-content="#content_card">
            <h4>
                <i class="fa fa-credit-card"></i>
                <?php echo JText::_('COM_SIMPLERENEW_CREDITCARD'); ?>
            </h4>
        </div>
    <?php
    endif;
    ?>
</div>
<!-- /.ost-section -->

<input type="hidden" id="billing_token" name="billing[token]" value=""/>

<?php
if (in_array('cc', $paymentOptions)) {
    echo $this->loadTemplate('creditcard');
}

if (in_array('pp', $paymentOptions)) {
    echo $this->loadTemplate('paypal');
}
