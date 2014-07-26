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
 * @var JObject                  $state
 */

$visible = $this->get('State')->get('coupon.default');
JHtml::_('sr.sliders', 'div.coupon-toggle', $visible);

$state = $this->get('State');
$couponCode = $state->get('coupon.default');

?>
<div class="ost-section">
    <div class="block6">
        <div class="coupon-toggle" data-content="#coupon-area">
            <?php echo JText::_('COM_SIMPLERENEW_COUPON_CODE_ASK'); ?>
        </div>

        <div id="coupon-area">
            <label for="coupon_code"><?php echo JText::_('COM_SIMPLERENEW_COUPON_CODE'); ?></label>

            <div class="input">
                <input
                    type="text"
                    name="coupon_code"
                    id="coupon_code"
                    value="<?php echo $couponCode; ?>"/>
            </div>
        </div>
    </div>
</div>
