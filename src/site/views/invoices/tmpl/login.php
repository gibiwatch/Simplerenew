<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-invoices'); ?>">
    <?php if ($this->getParams()->get('show_page_heading', true)): ?>
        <div class="page-header">
            <h1><?php echo $this->getHeading('COM_SIMPLERENEW_HEADING_INVOICES'); ?></h1>
        </div>
    <?php endif; ?>

    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_ERROR_LOGIN_REQUIRED'); ?>
    </div>
</div>
