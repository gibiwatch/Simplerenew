<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */
?>
<div class="ost-alert-warning m-bottom">
    <?php echo SimplerenewHelper::renderModule('simplerenew_cancel_cancel'); ?>
    <form
        id="formCancel"
        name="formCancel"
        action=""
        method="post">
        <button type="submit" class="btn btn-warning btn-small">
            <i class="fa fa-times"></i>
            <?php echo JText::_('COM_SIMPLERENEW_CANCEL_NODEALS'); ?>
        </button>
        <input
            type="hidden"
            name="option"
            value="com_simplerenew"/>
        <input
            type="hidden"
            name="task"
            value="renewal.cancel"/>
        <?php
        foreach ($this->subscriptions as $subscription) :
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
