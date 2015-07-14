<?php
/**
 * @package    Simplerenew
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div class="simplerenew-calculator">
    <h3><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_SELECTED_PLANS'); ?></h3>
    <div class="simplerenew-calculator-empty">
        <p><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_NONE_SELECTED'); ?></p>
    </div>
    <div class="simplerenew-calculator-display">
        <div class="simplerenew-calculator-items"></div>
        <div class="simplerenew-subtotal">
            <span><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_SUBTOTAL'); ?></span>
            <span class="simplerenew-subtotal-amount">0.00</span>
        </div>
        <div class="simplerenew-calculator-discount">
            <span><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_DISCOUNT'); ?></span>
            <span class="simplerenew-calculator-discount-amount">0.00</span>
        </div>
        <div class="simplerenew-calculator-total">
            <div><?php echo JText::_('COM_SIMPLERENEW_CALCULATOR_TOTAL'); ?></div>
            <div class="simplerenew-calculator-total-amount">0.00</div>
        </div>
    </div>
</div>
