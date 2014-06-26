<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<h4><?php echo JText::_('COM_SIMPLERENEW_HEADING_PLANLIST'); ?></h4>

<?php
foreach ($this->plans as $code => $plan):
    $plan_id = 'plan_code_' . $code;
    $classes = 'plan_code ' . $plan_id;
    $checked = $plan->selected ? ' checked="checked"' : '';
?>
<div class="<?php echo join(' ', $classes); ?>">
    <input<?php echo $checked; ?>
        type="radio"
        name="plan_code"
        id="<?php echo $plan_id; ?>"
        value="<?php echo $plan->code; ?>"/>

    <label for="<?php echo $plan_id; ?>">
        <strong>
            <?php echo JHtml::_('plan.name', $plan); ?>
        </strong>
    </label>
</div>

<?php
endforeach;

