<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if ($this->getParams()->get('show_page_heading', true)):
    ?>
    <div class="page-header">
        <h1>
            <?php
            echo $this->getHeading(
                JText::plural('COM_SIMPLERENEW_HEADING_RENEWAL_UPDATE', count($this->subscriptions), false)
            ); ?>
        </h1>
    </div>
    <?php
endif;
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <div class="ost-alert-notify">
        <?php echo JText::_('COM_SIMPLERENEW_RENEWAL_RESUBSCRIBE_INSTRUCTIONS'); ?>
    </div>
</div>
