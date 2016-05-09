<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$heading = $this->getHeading(
    JText::plural(
        'COM_SIMPLERENEW_HEADING_RENEWAL_UPDATE',
        count($this->subscriptions),
        false
    )
);

if ($heading):
    ?>
    <div class="page-header">
        <h1><?php echo $heading; ?></h1>
    </div>
    <?php
endif;
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_ERROR_LOGIN_REQUIRED'); ?>
    </div>
</div>
