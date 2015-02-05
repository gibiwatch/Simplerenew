<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal       $this
 * @var Simplerenew\Api\Subscription $subscription
 */
$app = SimplerenewFactory::getApplication();

$heading = $this->getHeading(
    JText::plural(
        'COM_SIMPLERENEW_HEADING_RENEWAL_UPDATE',
        count($this->subscriptions),
        false
    )
);
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <?php
    if ($heading):
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php
    endif;

    if (!$this->subscriptions):
        echo $this->loadTemplate('nosub');
    else:
        ?>
        <div class="ost-section p-bottom b-bottom">
            <?php echo JText::_('COM_SIMPLERENEW_RENEWAL_DESCRIPTION'); ?>
        </div>

        <form
            name="renewalForm"
            id="renewalForm"
            action=""
            method="post">

    <?php
        foreach ($this->subscriptions as $id => $subscription):
            $this->subscription = $subscription;
            ?>
            <div class="ost-section ost-row-two b-bottom">
                <?php echo $this->loadTemplate('plan'); ?>
            </div>
            <?php
        endforeach; ?>
            <input
                type="hidden"
                name="option"
                value="com_simplerenew"/>
            <input
                type="hidden"
                name="Itemid"
                value="<?php echo $app->input->getInt('Itemid'); ?>"/>

            <input
                type="hidden"
                name="task"
                value="renewal.update"/>

            <button type="submit" class="btn-main btn-big m-top">
                <span class="ost-text-enabled">
                    <i class="fa fa-check"></i>
                    <?php echo JText::_('COM_SIMPLERENEW_RENEWAL_UPDATE_BUTTON'); ?>
                </span>
                <span class="ost-text-disabled">
                    <i class="fa fa-spinner fa-spin"></i>
                    <?php echo JText::_('COM_SIMPLERENEW_RENEWAL_UPDATE_BUTTON_DISABLED'); ?>
                </span>
            </button>

            <?php echo JHtml::_('form.token'); ?>
        </form>
    <?php
    endif;
    ?>
</div>
