<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */
$app = SimplerenewFactory::getApplication();
$now = new DateTime();

$trials = array();
$billed = array();
foreach ($this->subscriptions as $subscription) {
    if ($now >= $subscription->trial_start && $now < $subscription->trial_end) {
        $trials[$subscription->id] = $subscription;
    } else {
        $billed[$subscription->id] = $subscription;
    }
}

$heading = $this->getHeading(
    JText::plural(
        'COM_SIMPLERENEW_HEADING_RENEWAL_UPDATE',
        count($this->subscriptions),
        false
    )
);
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <?php
    if ($heading) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;

    echo SimplerenewHelper::renderModule('simplerenew_cancel_top');

    if ($support = $this->funnel->get('support')) :
        echo SimplerenewHelper::renderModule('simplerenew_cancel_support');
        ?>
        <p><?php echo JHtml::_('link', JRoute::_('index.php?Itemid=' . $support), 'Contact Support'); ?></p>
        <?php
    endif;

    if ($trials && ($extendTrial = $this->funnel->get('extendTrial'))) :
        echo SimplerenewHelper::renderModule('simplerenew_cancel_trial');
        ?>
        <form
            id="formExtendTrial"
            name="formExtendTrial"
            action="index.php"
            method="post">
            <button type="submit" class="btn btn-main btn-small">
                <?php echo JText::sprintf('COM_SIMPLERENEW_CANCEL_EXTEND_TRIAL', $extendTrial); ?>
            </button>
        </form>
        <?php
    endif;

    if ($billed && ($pause = $this->funnel->get('pauseBilling'))) :
        $now      = new SRDateTime();
        $dateLimit = new SRDateTime();

        $dateLimit->addFromUserInput($pause);
        $dateDiff = $dateLimit->diff($now);

        echo SimplerenewHelper::renderModule('simplerenew_cancel_suspend');
        ?>
        <form
            id="formPauseBilling"
            name="formPauseBilling"
            action="index.php"
            method="post">
            <button type="submit" class="btn btn-main btn-small">
                <?php echo JText::sprintf('COM_SIMPLERENEW_CANCEL_PAUSE_BILLING', $pause); ?>
            </button>
        </form>
        <?php
    endif;

    if (($coupon = $this->funnel->get('offerCoupon'))) :
        if ($discount = $this->getDiscount($coupon, $this->subscriptions)) :
            echo SimplerenewHelper::renderModule('simplerenew_cancel_discount');
            ?>
            <p>
                <?php
                echo sprintf(
                    'I want to save %s on my next renewal!',
                    JHtml::_('currency.format', $discount)
                );
                ?>
            </p>
            <?php
        endif;
    endif;
    ?>
    <p>I'm not interested, just cancel my renewal</p>
    <?php
    echo SimplerenewHelper::renderModule('simplerenew_cancel_bottom');
    ?>
</div>
