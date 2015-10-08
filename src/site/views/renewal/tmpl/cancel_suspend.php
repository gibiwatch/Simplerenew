<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 * @var Subscription[]         $billed
 */

$app = SimplerenewFactory::getApplication();

$billed = array();
foreach ($this->subscriptions as $subscription) {
    if (!$subscription->inTrial()) {
        $billed[$subscription->id] = $subscription;
    }
}

if ($billed && ($suspend = $this->funnel->get('suspendBilling'))) :
    $now       = new SRDateTime();
    $dateLimit = new SRDateTime();

    $dateLimit->addFromUserInput($suspend);

    echo SimplerenewHelper::renderModule('simplerenew_cancel_suspend');
    ?>
    <form
        id="formSuspendBilling"
        name="formSuspendBilling"
        action=""
        method="post">
        <?php
        echo JText::sprintf(
            'COM_SIMPLERENEW_CANCEL_SUSPEND',
            JHtml::_('datetime.difference', $now, $suspend)
        );
        echo JHtml::_('calendar', $dateLimit->format('Y-m-d'), 'billingDate', 'billingDate');
        ?>
        <button type="submit" class="btn btn-main btn-small">
            <?php echo JText::_('COM_SIMPLERENEW_CANCEL_SUSPEND_BUTTON'); ?>
        </button>
        <input
            type="hidden"
            name="option"
            value="com_simplerenew"/>
        <input
            type="hidden"
            name="task"
            value="renewal.suspendBilling"/>
        <?php
        foreach ($billed as $subscription) :
            ?>
            <input
                type="hidden"
                name="ids[]"
                value="<?php echo $subscription->id; ?>"/>
            <?php
        endforeach;

        echo JHtml::_('form.token');
        ?>
    </form>
    <?php
endif;
