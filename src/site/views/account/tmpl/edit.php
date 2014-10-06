<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewAccount $this
 */
$app = SimplerenewFactory::getApplication();

JHtml::_('sr.validation.init', '#accountForm');
?>
<div class="ost-container simplerenew-edit-account">

    <?php if ($this->getParams()->get('show_page_heading', true)): ?>
    <div class="page-header">
        <h1><?php echo $this->getHeading('COM_SIMPLERENEW_ACCOUNT_EDIT'); ?></h1>
    </div>
    <?php endif; ?>

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
            <i class="fa fa-check"></i>
            <?php echo JText::_('COM_SIMPLERENEW_SAVE'); ?>
        </button>

        <span id="token">
            <?php echo JHtml::_('form.token'); ?>
        </span>
    </form>

</div>
