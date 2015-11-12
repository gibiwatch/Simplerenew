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
 * @var SimplerenewViewAccount $this
 * @var Subscription           $subscription
 */
$container = SimplerenewFactory::getContainer();

if ($this->subscriptions) :
    ?>
    <h3>
        <span><i class="fa fa-check"></i></span>
        <?php echo JText::plural('COM_SIMPLERENEW_HEADING_SUBSCRIPTION', count($this->subscriptions)); ?>
    </h3>
    <?php
    foreach ($this->subscriptions as $subscription) :
        $plan = $this->getPlan($subscription->plan);
        ?>
        <div class="ost-table">

            <div class="ost-section ost-row-one">
                <div class="block3">
                    <label><?php echo JText::_('COM_SIMPLERENEW_PLAN'); ?></label>
                </div>
                <div class="block9">
                    <?php echo $plan->name; ?>
                </div>
            </div>
            <!-- /.ost-section -->

        </div>
        <!-- .simplerenew-plan-information -->

        <div class="ost-subscriptions-list m-bottom">

            <?php
            if ($subscription->status == Subscription::STATUS_EXPIRED) :
                ?>
                <div class="ost-alert-warning">
                    <?php
                    echo JText::sprintf(
                        'COM_SIMPLERENEW_SUBSCRIPTION_EXPIRED',
                        $subscription->expires->format('F j, Y')
                    );
                    ?>
                </div>
                <?php
            else :
                ?>
                <div class="ost-alert-success">
                    <?php
                    $type = $subscription->inTrial() ? 'TRIAL' : 'ACTIVE';
                    echo JText::sprintf(
                        "COM_SIMPLERENEW_SUBSCRIPTION_PERIOD_{$type}",
                        $subscription->period_start->format('F j, Y'),
                        $subscription->period_end->format('F j, Y')
                    );
                    ?>
                </div>
                <?php
                if ($subscription->canceled) :
                    ?>
                    <div class="ost-alert-warning">
                        <?php echo JText::_('COM_SIMPLERENEW_SUBSCRIPTION_CANCELED'); ?>
                    </div>
                <?php
                elseif ($subscription->pending_plan) :
                    $pending = $container->getPlan()->load($subscription->pending_plan);
                    ?>
                    <div class="ost-alert-notify">
                        <?php
                        echo JText::sprintf(
                            'COM_SIMPLERENEW_SUBSCRIPTION_PENDING',
                            $subscription->period_end->format('F j, Y'),
                            $pending->name,
                            JHtml::_('currency.format', $pending->amount, $pending->currency)
                        );
                        ?>
                    </div>
                <?php
                else :
                    ?>
                    <div class="ost-alert-notify">
                        <?php
                        echo JText::sprintf(
                            "COM_SIMPLERENEW_SUBSCRIPTION_RENEW_DATE_{$type}",
                            $subscription->period_end->format('F j, Y')
                        );
                        ?>
                    </div>
                <?php
                endif;
            endif;
            ?>

        </div>
        <!-- .ost-subscriptions-list -->

    <?php
    endforeach;
else :
    ?>

    <div class="ost-alert-warning">
        <?php echo JText::_('COM_SIMPLERENEW_NON_SUBSCRIBER'); ?>
    </div>

<?php
endif;
