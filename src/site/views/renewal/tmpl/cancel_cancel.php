<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

echo SimplerenewHelper::renderModule('simplerenew_cancel_cancel');
?>
<form
    id="formCancel"
    name="formCancel"
    action=""
    method="post">
    <button type="submit" class="btn btn-main btn-small">
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
