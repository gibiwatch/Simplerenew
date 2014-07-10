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

?>
<div class="ost-container simplerenew-edit-account">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_ACCOUNT_EDIT'); ?></h1>
    </div>

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

        <input
            type="submit"
            value="<?php echo JText::_('COM_SIMPLERENEW_SAVE'); ?>"
            class="btn-main btn-big"/>

        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>
