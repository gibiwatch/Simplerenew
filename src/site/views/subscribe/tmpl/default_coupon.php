<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
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
    JHtml::_('sr.onready', "$('#coupon_code').valid();");
}

?>
<div class="ost-section p-bottom m-top b-bottom">
    <div class="block6">
        <div class="ost-coupon-box">

            <div class="coupon-toggle" data-content="#coupon-area">
                <?php echo JText::_('COM_SIMPLERENEW_COUPON_CODE_ASK'); ?>
            </div>

            <div id="coupon-area">
                <div class="input">
                    <input
                        type="text"
                        name="couponCode"
                        id="coupon_code"
                        value="<?php echo $this->escape($couponCode); ?>"
                        class="check_coupon"
                        data-plan=".plan-code input"/>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /ost-section -->
