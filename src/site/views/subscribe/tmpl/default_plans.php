<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

echo $this->stepHeading(JText::_('COM_SIMPLERENEW_HEADING_PLANLIST'));
?>
<div class="ost-section">
    <div class="block12 p-bottom b-bottom">
        <?php
        foreach ($this->plans as $code => $plan):
            $planId   = 'plan_code_' . $code;
            $classes  = 'plan_code ' . $planId;
            $checked  = $plan->selected ? ' checked' : '';
            $disabled = $plan->disabled ? ' disabled' : '';
            ?>
            <div class="<?php echo $classes; ?>">
                <span class="simplerenew-plan <?php echo $planId . $disabled; ?>">
                    <input<?php echo $checked . $disabled; ?>
                        type="radio"
                        name="planCode"
                        id="<?php echo $planId; ?>"
                        value="<?php echo $plan->code; ?>"/>
                    <?php echo JHtml::_('plan.name', $plan); ?>
                </span>
            </div>

        <?php
        endforeach; ?>
    </div>
</div>
<!-- /.ost-section -->

