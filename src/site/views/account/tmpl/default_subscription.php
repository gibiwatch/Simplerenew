<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if ($this->subscription):
    ?>
    <h3><?php echo JText::_('COM_SIMPLERENEW_HEADING_SUBSCRIPTION'); ?></h3>

    <div class="ost-section">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_PLAN'); ?></label>
            <?php echo $this->subscription->plan->name; ?>
        </div>
    </div>

    <div class="ost-section">
        <div class="block6">
            <label><?php echo JText::_('COM_SIMPLERENEW_PERIOD_END'); ?></label>
            <?php
            echo JHtml::_(
                'datetime.format',
                $this->subscription->period_end,
                JText::_('COM_SIMPLERENEW_NO_PERIOD_END')
            );
            ?>
        </div>
    </div>

    <?php
else:
    echo JText::_('COM_SIMPLERENEW_NON_SUBSCRIBER');
endif;
