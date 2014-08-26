<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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

JHtml::_('script', 'https://js.recurly.com/v3/recurly.js');

?>
<script>
    (function($) {
        $.Simplerenew.form = $.extend({}, $.Simplerenew, {
            init: function(form) {
                recurly.configure('sc-mKTZVTAwfHWE7HQh0vsujW');

                $(document.getElementById('billing_cc_number')).attr('data-recurly', 'number');
                $(document.getElementById('billing_cc_month')).attr('data-recurly', 'month');
                $(document.getElementById('billing_cc_year')).attr('data-recurly', 'year');
                $(document.getElementById('billing_cc_cvv')).attr('data-recurly', 'cvv');
                $(document.getElementById('billing_firstname')).attr('data-recurly', 'first_name');
                $(document.getElementById('billing_lastname')).attr('data-recurly', 'last_name');
                $(document.getElementById('billing_address1')).attr('data-recurly', 'address1');
                $(document.getElementById('billing_address2')).attr('data-recurly', 'address2');
                $(document.getElementById('billing_city')).attr('data-recurly', 'city');
                $(document.getElementById('billing_region')).attr('data-recurly', 'state');
                $(document.getElementById('billing_postal')).attr('data-recurly', 'postal_code');
                $(document.getElementById('billing_country')).attr('data-recurly', 'country');
            },

            submit: function(form) {
                var method = $(form).find('input[name=payment_method]:enabled').val();
                var billing_token = document.getElementById('billing_token');

                billing_token.value = '';
                switch (method) {
                    case 'cc':
                        var number = $(form).find('#billing_cc_number').val();

                        if (number) {
                            recurly.token(form, function(err, token) {
                                if (err) {
                                    alert(err.message);
                                } else {
                                    billing_token.value = token.id;
                                    form.submit();
                                }
                            });
                        } else {
                            form.submit();
                        }
                        break;

                    case 'pp':
                        alert('Paypal is not supported yet');
                        break;
                }
            }
        });
    })(jQuery);
</script>
<div id="content_card" class="content-enabled">
    <input type="hidden" name="payment_method" value="cc"/>

    <div class="p-full">
        <div class="ost-section">
            <div class="block6">
                <label for="billing_firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                <input
                    id="billing_firstname"
                    name="billing[firstname]"
                    type="text"
                    value="<?php echo $this->billing->firstname; ?>"
                    required="true"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_FIRSTNAME_REQUIRED'); ?>"/>
            </div>
            <div class="block6">
                <label for="billing_lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
                <input
                    id="billing_lastname"
                    name="billing[lastname]"
                    type="text"
                    value="<?php echo $this->billing->lastname; ?>"
                    required="true"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_LASTNAME_REQUIRED'); ?>"/>
            </div>
        </div>
        <!-- /.ost-section -->

        <?php echo $this->loadTemplate('address'); ?>

        <div class="ost-section">
            <div class="block5">
                <label><?php echo JText::_('COM_SIMPLERENEW_CC_NUMBER'); ?></label>
                <input
                    <?php
                    if (!$this->billing->payment->exists()) {
                        echo 'required="true"';
                    }
                    ?>
                    id="billing_cc_number"
                    type="text"
                    value=""
                    placeholder="<?php echo JHtml::_('creditcard.mask', $creditCard->lastFour); ?>"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_NUMBER_REQUIRED'); ?>"/>
            </div>
            <div class="block2">
                <label for="billing_cc_cvv">
                    <?php echo JText::_('COM_SIMPLERENEW_CC_CVV'); ?>
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
                    class="small-width"
                    data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_CC_CVV_REQUIRED'); ?>"/>
            </div>
            <div class="block5">
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
