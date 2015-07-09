<?php
/**
 * @package    Simplerenew
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="calculator">
    <h3><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_SELECTED_PLANS'); ?></h3>
    <div class="simplerenew-calculator-empty">
        <p><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_NONE_SELECTED'); ?></p>
    </div>
    <div class="simplerenew-calculator-display">
        <div class="simplerenew-calculator-items"></div>
        <div class="simplerenew-subtotal">
            <div class="simplerenew-subtotal-subtotal">
                <span><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_SUBTOTAL'); ?></span>
                <span class="simplerenew-amount">0.00</span>
            </div>
            <div class="simplerenew-subtotal-discount">
                <span><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_DISCOUNT'); ?></span>
                <span class="simplerenew-discount">0.00</span>
            </div>
        </div>
        <div class="simplerenew-total">
            <div><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_TOTAL'); ?></div>
            <div class="simplerenew-amount">0.00</div>
        </div>
    </div>
</div>
