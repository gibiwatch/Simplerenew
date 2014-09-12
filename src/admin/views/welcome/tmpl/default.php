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
JHtml::_('behavior.modal');
JHtml::_('stylesheet', 'com_simplerenew/grid.css', null, true);
JHtml::_('stylesheet', 'com_simplerenew/grid-responsive.css', null, true);
JHtml::_('stylesheet', 'com_simplerenew/admin.css', null, true);

$status = new SimplerenewStatus();
?>

<div class="ost-container">

    <div class="ost-section ost-steps">
        <div class="block2">
            <?php echo JHtml::_('image', 'com_simplerenew/logo.png', '', null, true); ?>
        </div>
        <div class="block1 ost-connector">
            <?php echo JHtml::_('image', 'com_simplerenew/points.png', '', null, true); ?>
        </div>
        <div class="block2">
            <?php
            echo $this->renderStep(
                $status->gateway,
                'gateway',
                JRoute::_('index.php?option=com_config&view=component&component=com_simplerenew&path=&tmpl=component'),
                array(
                    'class' => 'btn btn-small modal',
                    'rel'   => "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
                )
            );
            ?>
        </div>
        <div class="block2">
            <?php
            echo $this->renderStep(
                $status->plans,
                'plans',
                JRoute::_('index.php?option=com_simplerenew&task=plan.add'),
                'class="btn btn-small"'
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
