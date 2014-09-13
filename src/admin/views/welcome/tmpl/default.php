<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var SimplerenewViewWelcome $this */

// Load support assets
JHtml::_('stylesheet', 'com_simplerenew/grid.css', null, true);
JHtml::_('stylesheet', 'com_simplerenew/grid-responsive.css', null, true);
JHtml::_('stylesheet', 'com_simplerenew/admin.css', null, true);

// Setup for configuration options
$optionsLink = JRoute::_('index.php?option=com_config&view=component&component=com_simplerenew&path=&tmpl=component');
if (version_compare(JVERSION, '3', 'ge')) {
    $optionsAttribs = 'class ="btn btn-small';
} else {
    JHtml::_('behavior.modal');
    $optionsAttribs = array(
        'class' => 'btn btn-small modal',
        'rel'   => "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
    );
}

$status = new SimplerenewStatus();
?>

<div class="ost-container ost-container-dashboard">

    <div class="ost-section ost-steps">
        <div class="block2">
            <?php echo JHtml::_('image', 'com_simplerenew/logo.png', '', null, true); ?>
        </div>
        <div class="block1 ost-connector">
            <?php echo JHtml::_('image', 'com_simplerenew/points.png', '', null, true); ?>
        </div>
        <div class="block2">
            <?php echo $this->renderStep($status->gateway, 'gateway', $optionsLink, $optionsAttribs);?>
        </div>
        <div class="block2">
            <?php
            $planLink = JRoute::_('index.php?option=com_simplerenew&task=plan.add');
            echo $this->renderStep(
                $status->gateway && $status->plans,
                ($status->gateway ? 'plans' : 'plans_gateway'),
                ($status->gateway ? $planLink : $optionsLink),
                ($status->gateway ? 'class="btn btn-small"' : $optionsAttribs)
            );
            ?>
        </div>
        <div class="block2">
            <?php
            echo $this->renderStep(
                $status->subscribe,
                'subscribeform',
                JRoute::_('index.php?option=com_menus'),
                'class="btn btn-small"'
            );
            ?>
        </div>
    </div>
    <!-- /.ost-section -->
</div>
<!-- /.ost-container -->
