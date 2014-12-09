<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

$couponCode = $this->state->get('coupon.default');
$planSelected = false;
foreach ($this->plans as $plan) {
    $planSelected |= $plan->selected;
}

JHtml::_('sr.sliders', 'div.coupon-toggle', $couponCode);
if ($couponCode && $planSelected) {
    JHtml::_('sr.onready', "jQuery('#coupon_code').valid();");
}

?>
<div class="ost-section">
    <div class="block6 m-top p-bottom b-bottom">
        <div class="ost-alert-notify">

            <div class="coupon-toggle" data-content="#coupon-area">
                <?php echo JText::_('COM_SIMPLERENEW_COUPON_CODE_ASK'); ?>
            </div>

            <div id="coupon-area">
                <div class="input">
                    <input
                        type="text"
                        name="couponCode"
                        id="coupon_code"
                        value="<?php echo $couponCode; ?>"
                        class="check_coupon"
                        data-plan=".plan_code input"/>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /ost-section -->
