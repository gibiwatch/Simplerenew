<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewAccount $this
 */
$app = SimplerenewFactory::getApplication();

JHtml::_('sr.validation.init', '#accountForm');
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-edit-account'); ?>">
    <?php
    if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_EDIT_ACCOUNT')) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;
    ?>

    <form
        name="accountForm"
        id="accountForm"
        action=""
        method="post">

        <?php echo $this->loadTemplate('account'); ?>

        <?php echo $this->loadTemplate('billing'); ?>

        <input
            type="hidden"
            name="userId"
            value="<?php echo $this->user->id; ?>"/>

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
            value="account.save"/>

        <button type="submit" class="btn-main btn-big">
            <span class="ost-text-enabled">
                <i class="fa fa-check"></i>
                <?php echo JText::_('COM_SIMPLERENEW_SAVE'); ?>
            </span>
            <span class="ost-text-disabled">
                <i class="fa fa-spinner fa-spin"></i>
                <?php echo JText::_('COM_SIMPLERENEW_SAVE_DISABLED'); ?>
            </span>
        </button>

        <span id="token">
            <?php echo JHtml::_('form.token'); ?>
        </span>
    </form>
</div>
