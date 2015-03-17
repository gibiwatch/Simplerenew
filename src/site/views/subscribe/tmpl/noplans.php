<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-subscribe'); ?>">
    <?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_SUBSCRIBE')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <div class="ost-alert-warning">
        <?php
        if ($this->getParams()->get('basic.allowMultiple')) {
            echo JText::_('COM_SIMPLERENEW_SUBSCRIPTION_ALL_SUBSCRIBED');
        } else {
            echo JText::_('COM_SIMPLERENEW_SUBSCRIPTION_NOPLANS_AVAILABLE');
        }
        ?>
    </div>
</div>
