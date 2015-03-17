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
    <?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_ACCOUNT_INFO')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_ERROR_LOGIN_REQUIRED'); ?>
    </div>
</div>
