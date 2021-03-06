<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 * @var Subscription[]         $trials
 */

$app    = SimplerenewFactory::getApplication();
$now    = new DateTime();
$trials = array();

foreach ($this->subscriptions as $subscription) {
    if ($subscription->inTrial()) {
        $trials[$subscription->id] = $subscription;
    }
}

if ($trials && ($extendTrial = $this->funnel->get('extendTrial'))) :
    ?>
    <div class="ost-alert-notify m-bottom">
        <?php echo SimplerenewHelper::renderModule('simplerenew_cancel_trial'); ?>
        <form
            id="formExtendTrial"
            name="formExtendTrial"
            action=""
            method="post">
            <button type="submit" class="btn btn-main btn-small">
                <i class="fa fa-plus-circle"></i>
                <?php echo JText::sprintf('COM_SIMPLERENEW_CANCEL_EXTEND_TRIAL', $extendTrial); ?>
            </button>
            <input
                type="hidden"
                name="option"
                value="com_simplerenew"/>
            <input
                type="hidden"
                name="task"
                value="renewal.extendTrial"/>
            <input
                type="hidden"
                name="intervalDays"
                value="<?php echo $extendTrial; ?>"/>
            <?php
            foreach ($trials as $subscription) :
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
    </div>
    <?php
endif;
