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
 * @var SimplerenewViewAccount $this
 */

if ($this->subscription):
    ?>
    <h3><span><i class="fa fa-check"></i></span> <?php echo JText::_('COM_SIMPLERENEW_HEADING_SUBSCRIPTION'); ?></h3>

    <div class="ost-section ost-row-one">
        <div class="block3">
            <label><?php echo JText::_('COM_SIMPLERENEW_PLAN'); ?></label>
        </div>
        <div class="block9">
            <?php echo $this->plan->name; ?>
        </div>
    </div>
    <!-- /.ost-section -->

    <?php
    if ($this->subscription->status == Subscription::STATUS_EXPIRED):
        ?>
        <div class="ost-alert-warning">
            <?php
             echo JText::sprintf(
                'COM_SIMPLERENEW_SUBSCRIPTION_EXPIRED',
                $this->subscription->expires->format('F j, Y')
             );
             ?>
             <br/>
             <?php
             JHtml::_(
                'link',
                '#',
                JText::_('COM_SIMPLERENEW_SUBSCRIPTION_RESUBCRIBE'),
                'onclick="alert(\'Under Construction\');return false;"'
             );
             ?>
        </div>
    <?php
    else:
        ?>
        <div class="ost-alert-success">
            <?php
            echo JText::sprintf(
                'COM_SIMPLERENEW_SUBSCRIPTION_ACTIVE_PERIOD',
                $this->subscription->period_start->format('F j, Y'),
                $this->subscription->period_end->format('F j, Y')
            );
            ?>
        </div>
    <?php
        if ($this->subscription->canceled):
            ?>
            <div class="ost-alert-notify">
                <?php echo JText::_('COM_SIMPLERENEW_SUBSCRIPTION_CANCELED'); ?>
            </div>
    <?php
        elseif ($this->pending):
            ?>
            <div class="ost-alert-notify">
                <?php
                echo JText::sprintf(
                    'COM_SIMPLERENEW_SUBSCRIPTION_PENDING',
                    $this->subscription->period_end->format('F j, Y'),
                    $this->pending->name,
                    '$' . number_format($this->pending->amount, 2)
                );
                ?>
            </div>
    <?php
        else:
                ?>
            <div class="ost-alert-notify">
                <?php
                echo JText::sprintf(
                    'COM_SIMPLERENEW_SUBSCRIPTION_RENEW_DATE',
                    $this->subscription->period_end->format('F j, Y')
                );
                ?>
            </div>
    <?php
        endif;
    endif;
else:
    ?>

    <div class="ost-alert-notify">
        <?php echo JText::_('COM_SIMPLERENEW_NON_SUBSCRIBER'); ?>
    </div>

<?php
endif;
