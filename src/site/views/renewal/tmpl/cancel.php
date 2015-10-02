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

$funnel = (object)$this->getParams()->get('funnel');
$ids    = $app->input->get('ids', array(), 'array');

echo SimplerenewHelper::renderModule('simplerenew_cancel_top');

if (!empty($funnel->support)) :
    echo SimplerenewHelper::renderModule('simplerenew_cancel_support');
    ?>
    <p><?php echo JHtml::_('link', JRoute::_('index.php?Itemid=' . $funnel->support), 'Contact Support'); ?></p>
    <?php
endif;

if (!empty($funnel->extendTrial)) :
    echo SimplerenewHelper::renderModule('simplerenew_cancel_trial');
    ?>
    <p><?php echo sprintf('Offer to extend trial by %s days', $funnel->extendTrial); ?></p>
    <?php
endif;

if (!empty($funnel->pauseBilling)) :
    $now       = new SRDateTime();
    $dateLimit = new SRDateTime();

    $dateLimit->addFromUserInput($funnel->pauseBilling);
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

if (!empty($funnel->offerCoupon)) :
    if ($discount = $this->getDiscount($funnel->offerCoupon, $ids)) :
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
