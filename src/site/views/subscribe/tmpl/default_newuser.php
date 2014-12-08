<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

?>
<?php if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_SUBSCRIBE')): ?>
<div class="page-header">
    <h1><?php echo $heading; ?></h1>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('account'); ?>

<div class="ost-section">

    <?php
    echo SimplerenewHelper::renderModule('simplerenew_plans_top');
    echo $this->loadTemplate($this->allowMultiple ? 'mplans' : 'plans');
    echo SimplerenewHelper::renderModule('simplerenew_plans_bottom');

    $showCoupon = $this->state->get('coupon.allow');
    if ($showCoupon < 0 || $showCoupon == 1) {
        echo $this->loadTemplate('coupon');
    }
    ?>

    <?php echo $this->loadtemplate('billing'); ?>

    <div class="m-bottom m-top">
        <?php echo JHtml::_('sr.terms'); ?>
    </div>

    <?php echo SimplerenewHelper::renderModule('simplerenew_submit_top'); ?>
    <div class="m-bottom">
        <button type="submit" class="btn-main btn-big">
            <i class="fa fa-check"></i>
            <?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE_BUTTON'); ?>
        </button>
    </div>
    <?php echo SimplerenewHelper::renderModule('simplerenew_submit_bottom'); ?>
</div>
<!-- /.ost-section -->

<input
    type="hidden"
    name="task"
    value="subscription.create"/>
