<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="ost-section content-enabled" id="content_paypal">
    <div class="block12">
        <div class="p-full">
            <p><?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?></p>
            <?php
            if ($this->getParams()->get('basic.paypalWarning', 0)) :
                ?>
                <div class="ost-alert ost-alert-notify"><?php echo JText::_('COM_SIMPLERENEW_POPUP_PAYPAL_WARN'); ?></div>
                <?php
            endif;
            ?>
            <input type="hidden" name="payment_method" value="pp"/>
        </div>
    </div>
</div>
<!-- /.ost-section -->
