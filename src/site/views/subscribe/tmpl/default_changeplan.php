<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

reset($this->subscriptions);
$current = current($this->subscriptions);

?>
<?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_CHANGE_PLAN')): ?>
    <div class="page-header">
        <h1><?php echo $heading; ?></h1>
    </div>
<?php endif; ?>

<?php if ($this->getParams()->get('basic.enableUpgrade')) : ?>
    <div class="ost-alert-notify m-bottom">
        <?php echo JText::_('COM_SIMPLERENEW_SUBSCRIPTION_PLAN_CHANGE'); ?>
    </div>
<?php endif; ?>

<div class="ost-section">

    <?php
    echo SimplerenewHelper::renderModule('simplerenew_plans_top');
    echo $this->loadTemplate('plans');
    echo SimplerenewHelper::renderModule('simplerenew_plans_bottom');

    $showCoupon = $this->state->get('coupon.allow');
    if ($showCoupon < 0 || $showCoupon == 2) {
        echo $this->loadTemplate('coupon');
    }
    ?>

    <?php echo $this->loadtemplate('billing'); ?>

    <?php echo SimplerenewHelper::renderModule('simplerenew_submit_top'); ?>
    <div class="m-bottom m-top">
        <button type="submit" class="btn-main btn-big">
            <span class="ost-text-enabled">
                <i class="fa fa-refresh"></i>
                <?php echo JText::_('COM_SIMPLERENEW_CHANGE_BUTTON'); ?>
            </span>
            <span class="ost-text-disabled">
                <i class="fa fa-spinner fa-spin"></i>
                <?php echo JText::_('COM_SIMPLERENEW_CHANGE_BUTTON_DISABLED'); ?>
            </span>
        </button>
    </div>
    <?php echo SimplerenewHelper::renderModule('simplerenew_submit_bottom'); ?>
</div>
<!-- /.ost-section -->

<input
    type="hidden"
    name="task"
    value="subscription.change"/>

<input
    type="hidden"
    name="id"
    value="<?php echo $current->id; ?>"/>
