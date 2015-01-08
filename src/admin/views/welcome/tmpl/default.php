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

$status = SimplerenewFactory::getStatus();
?>
<div class="ost-container ost-container-dashboard">

    <div class="ost-section ost-steps">
        <div class="block2">
            <?php echo JHtml::_('image', 'com_simplerenew/logo.png', '', null, true); ?>
        </div>
        <div class="block1 ost-connector"> </div>
        <div class="block2">
            <?php
            $link = JHtml::_(
                'link.options',
                '<span class="icon-plus"></span>' . JText::_('COM_SIMPLERENEW_WELCOME_GATEWAY_LINKTEXT')
            );
            echo $this->renderStep($status->gateway, 'gateway', $link);
            ?>
        </div>

        <?php if ($status->gateway): ?>
        <div class="block2">
            <?php
            $link = JHtml::_(
                'link',
                JRoute::_('index.php?option=com_simplerenew&task=plan.add'),
                '<span class="icon-plus"></span>' . JText::_('COM_SIMPLERENEW_WELCOME_PLANS_LINKTEXT'),
                'class="btn btn-small"'
            );

            echo $this->renderStep($status->plans, 'plans', $link);
            ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- /.ost-section -->
</div>
<!-- /.ost-container -->
