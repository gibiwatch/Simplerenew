<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

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
<div class="ost-section p-bottom b-bottom">
    <div class="block12">
        <?php
        foreach ($this->plans as $code => $plan):
            $planId  = 'plan-code-' . $code;
            $checked = $plan->selected ? ' checked' : '';
            ?>
            <div class="<?php echo 'plan-code ' . $planId; ?>">
                <span class="<?php echo 'simplerenew-plan user-group-' . $plan->group_id; ?>">
                    <input<?php echo $checked; ?>
                        id="<?php echo $planId; ?>"
                        name="planCodes[]"
                        type="checkbox"
                        value="<?php echo $plan->code; ?>"
                        required="true"
                        data-description="<?php echo $plan->name; ?>"
                        data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PLAN_REQUIRED'); ?>"
                        data-error-placement="#plancode-error"/>
                    <?php
                    echo JHtml::_(
                        'plan.name',
                        $plan->name,
                        $plan->currency,
                        $plan->amount,
                        $plan->length,
                        $plan->unit
                    );
                    ?>
                </span>
                <?php
                if ($plan->trial_length > 0 && $plan->trial_unit) :
                ?>
                    <br class="ost-breakline-mobile" />
                    <span class="simplerenew-plan-trial">
                        <?php
                        echo JHtml::_(
                            'plan.trial',
                            $plan->trial_length,
                            $plan->trial_unit
                        );
                        ?>
                    </span>
                <?php
                endif; ?>
            </div>

        <?php
        endforeach; ?>
        <div id="plancode-error"></div>
    </div>
</div>
<!-- /.ost-section -->
