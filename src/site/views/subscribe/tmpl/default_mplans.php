<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
<div class="ost-section">
    <div class="block12">
        <?php
        foreach ($this->plans as $code => $plan):
            $planId  = 'plan_code_' . $code;
            $classes = array('plan_code', $planId);

            $active = false;
            if ($plan->subscription && !empty($this->subscriptions[$plan->subscription])) {
                $classes[] = 'subscriber';

                $subscription = $this->subscriptions[$plan->subscription];
                $active = ($subscription->status == Subscription::STATUS_ACTIVE);
            }
            $checked = $plan->selected && $active ? ' checked' : '';
            ?>
            <div class="<?php echo join(' ', $classes); ?>">
                <span class="simplerenew-plan <?php echo $planId; ?>">
                    <input<?php echo $checked; ?>
                        type="checkbox"
                        name="planCode"
                        id="<?php echo $planId; ?>"
                        value="<?php echo $plan->code; ?>"
                        data-description="<?php echo $plan->name; ?>"/>
                    <?php
                    echo JHtml::_(
                        'plan.name',
                        $plan->name,
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
