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
    <h3><?php echo JText::_('COM_SIMPLERENEW_HEADING_SUBSCRIPTION'); ?></h3>

    <div class="ost-section">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_PLAN'); ?></label>
            <?php echo $this->plan->name; ?>
        </div>
    </div>

    <?php
    if ($this->subscription->status == Subscription::STATUS_EXPIRED):
        ?>
        <div class="ost_section">
            <div class="block6">
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
        </div>
    <?php
    else:
        ?>
        <div class="ost-section">
            <div class="block6">
                <?php
                echo JText::sprintf(
                    'COM_SIMPLERENEW_SUBSCRIPTION_ACTIVE_PERIOD',
                    $this->subscription->period_start->format('F j, Y'),
                    $this->subscription->period_end->format('F j, Y')
                );
                ?>
            </div>
        </div>
    <?php
    endif;
else:
    echo JText::_('COM_SIMPLERENEW_NON_SUBSCRIBER');
endif;
