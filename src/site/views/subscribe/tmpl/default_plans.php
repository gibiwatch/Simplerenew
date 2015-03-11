<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

JHtml::_(
    'sr.clickarea',
    array(
        'selector'    => '.simplerenew-plan',
        'selectClass' => 'simplerenew-plan-selected'
    )
);

echo $this->stepHeading(JText::plural('COM_SIMPLERENEW_HEADING_PLANLIST', count($this->plans)));
?>
<div class="ost-section">
    <div class="block12">
        <?php
        foreach ($this->plans as $code => $plan):
            $planId  = 'plan-code-' . $code;
            $checked = $plan->selected ? ' checked' : '';
            ?>
            <div class="<?php echo 'plan-code ' . $planId; ?>">
                <span class="<?php echo 'simplerenew-plan user-group-' . $plan->group_id; ?>">
                    <input<?php echo $checked; ?>
                        type="radio"
                        name="planCodes"
                        id="<?php echo $planId; ?>"
                        value="<?php echo $plan->code; ?>"
                        data-description="<?php echo $plan->name; ?>"/>
                    <?php
                    echo JHtml::_(
                        'plan.name',
                        $plan->name,
                        $plan->currency,
                        $plan->amount,
                        $plan->length,
                        $plan->unit,
                        $plan->trial_length,
                        $plan->trial_unit
                    );
                    ?>
                </span>
            </div>

        <?php
        endforeach; ?>
    </div>
</div>
<!-- /.ost-section -->

