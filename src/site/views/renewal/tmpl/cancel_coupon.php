<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

if (($couponCode = $this->funnel->get('offerCoupon'))) :
    if ($coupon = $this->validateCoupon($couponCode, $this->subscriptions)) :
        echo SimplerenewHelper::renderModule('simplerenew_cancel_coupon');
        ?>
        <form
            id="formOfferCoupon"
            name="formOfferCoupon"
            action=""
            method="post">
            <button type="submit" class="btn btn-main btn-small">
                <i class="fa fa-tag"></i> <?php
                echo JText::sprintf(
                    'COM_SIMPLERENEW_CANCEL_OFFER_COUPON',
                    $coupon->amountAsString()
                );
                ?>
            </button>
            <input
                type="hidden"
                name="option"
                value="com_simplerenew"/>
            <input
                type="hidden"
                name="task"
                value="renewal.offerCoupon"/>
            <input
                type="hidden"
                name="coupon"
                value="<?php echo $coupon->code; ?>"/>
            <?php
            foreach ($this->subscriptions as $subscription) :
                ?>
                <input
                    type="hidden"
                    name="ids[]"
                    value="<?php echo $subscription->id; ?>"/>
                <?php
            endforeach;

            echo JHtml::_('form.token');
            ?>
        </form>
        <?php
    endif;
endif;
