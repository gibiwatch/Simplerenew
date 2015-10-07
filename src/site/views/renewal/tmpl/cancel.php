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
    <p><?php echo sprintf('Offer to extend trial by %s days', $extendTrial); ?></p>
    <?php
endif;

if ($billed && ($pause = $this->funnel->get('pauseBilling'))) :
    $now       = new SRDateTime();
    $dateLimit = new SRDateTime();

    $dateLimit->addFromUserInput($pause);
    $dateDiff = $dateLimit->diff($now);

    echo SimplerenewHelper::renderModule('simplerenew_cancel_suspend');
    ?>
    <p>
        <?php
        echo sprintf(
            'Offer to suspend billing for as long as %s days (%s)',
            $dateDiff->format('%a'),
            $dateLimit->format('Y-m-d')
        );
        ?>
    </p>
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
