<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

$funnel = (object)$this->getParams()->get('funnel');
$ids    = SimplerenewFactory::getApplication()->input->get('ids');

if (!empty($funnel->support)) :
    ?>
    <p><?php echo JHtml::_('link', JRoute::_('index.php?Itemid=' . $funnel->suport), 'Contact Support'); ?></p>
    <?php
endif;

if (!empty($funnel->extendTrial)) :
    ?>
    <p><?php echo sprintf('Offer to extend trial by %s days', $funnel->extendTrial); ?></p>
    <?php
endif;

if (!empty($funnel->pauseBilling) && $dateLimit = $this->getDateDiff($funnel->pauseBilling)) :
    $now      = new DateTime();
    $dateDiff = $dateLimit->diff($now);
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

if ($discount = $this->getDiscount($funnel)) :
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
?>
<p>I'm not interested, just cancel my renewal</p>
