<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="ost-section content-enabled" id="content_paypal">
    <div class="block12">
        <div class="p-full">
            <?php echo JText::_('COM_SIMPLERENEW_PROCEED_TO_PAYPAL'); ?>
            <input type="hidden" name="payment_method" value="pp"/>
        </div>
    </div>
</div>
<!-- /.ost-section -->
