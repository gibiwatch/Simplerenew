<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<h3><?php echo JText::_('COM_SIMPLERENEW_HEADING_PLANLIST'); ?></h3>

<div class="ost-section">
    <div class="block12 p-bottom b-bottom">
        <?php
        foreach ($this->plans as $code => $plan):
            $planId = 'plan_code_' . $code;
            $classes = 'plan_code ' . $planId;
            $checked = $plan->selected ? ' checked="checked"' : '';
        ?>
        <div class="<?php echo join(' ', $classes); ?>">
            <input<?php echo $checked; ?>
                type="radio"
                name="planCode"
                id="<?php echo $planId; ?>"
                value="<?php echo $plan->code; ?>"/>

            <span class="simplerenew-plan <?php echo $planId; ?>">
                <?php echo JHtml::_('plan.name', $plan); ?>
            </span>
        </div>

        <?php endforeach; ?>
    </div>
</div>
<!-- /.ost-section -->

