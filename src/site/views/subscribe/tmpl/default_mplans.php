<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
<div class="ost-section  p-bottom b-bottom ost-plans-list">
    <div class="block12">
        <?php
        foreach ($this->plans as $code => $plan) :
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
                        class="validate"
                        data-description="<?php echo $plan->name; ?>"
                        data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PLAN_REQUIRED'); ?>"
                        data-error-placement="#plancode-error"/>
                    <?php
                    // Plan name
                    echo JHtml::_(
                        'plan.name',
                        $plan->name,
                        $plan->currency,
                        $plan->amount,
                        $plan->length,
                        $plan->unit
                    );
                    // Plan setup
                    if ($plan->setup_cost > 0) :
                        echo ' + '
                            . JHtml::_(
                                'currency.format',
                                $plan->setup_cost,
                                $plan->currency
                            )
                            . ' ' . JText::_('COM_SIMPLERENEW_PLAN_SETUP_COST_LABEL');
                    endif;
                    ?>
                </span>
                <?php
                // Plan Trial
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
        <?php
        if ($this->getParams()->get('basic.showCalculator', 0)) {
            echo $this->loadTemplate('calculator');
        }
        ?>
    </div>
</div>
<!-- /.ost-section -->
