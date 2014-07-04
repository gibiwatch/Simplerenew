<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="ost-container simplerenew-subscribe">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_ACCOUNT_INFO'); ?></h1>
    </div>

    <div class="ost-section">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
            <?php echo $this->user->firstname; ?>
        </div>
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
            <?php echo $this->user->lastname; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section p-bottom b-bottom">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?></label>
            <?php echo $this->user->username; ?>
        </div>
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?></label>
            <?php echo $this->user->email; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section p-bottom b-bottom">
        <div class="block12">
            <label><?php echo JText::_('COM_SIMPLERENEW_MEMBER_GROUP'); ?></label>
            <?php echo $this->user->groupText; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <?php echo $this->loadTemplate('billing'); ?>

    <?php echo $this->loadTemplate('subscription'); ?>
</div>
