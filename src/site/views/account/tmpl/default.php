<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-account'); ?>">
    <?php
    if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_ACCOUNT_INFO')):
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php
    endif; ?>

    <h3><span><i class="fa fa-info-circle"></i></span> <?php echo JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION'); ?></h3>

    <div class="m-bottom b-bottom ost-table">

        <div class="ost-section ost-row-one">
            <div class="block3">
                <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
            </div>
            <div class="block9">
                <?php echo $this->user->firstname; ?>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section ost-row-two">
            <div class="block3">
                <label><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
            </div>
            <div class="block9">
                <?php echo $this->user->lastname; ?>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section ost-row-one">
            <div class="block3">
                <label><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?></label>
            </div>
            <div class="block9">
                <?php echo $this->user->username; ?>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section ost-row-two">
            <div class="block3">
                <label><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?></label>
            </div>
            <div class="block9">
                <?php echo $this->user->email; ?>
            </div>
        </div>
        <!-- /.ost-section -->

    </div>
    <!-- .simplerenew-basic-information -->

    <?php echo $this->loadTemplate('billing'); ?>

    <?php echo $this->loadTemplate('subscription'); ?>
</div>
