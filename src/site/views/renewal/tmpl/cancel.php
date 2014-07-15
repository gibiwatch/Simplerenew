<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

$app = SimplerenewFactory::getApplication();

?>
<div class="ost-container simplerenew-renewal">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_RENEWAL_CANCEL'); ?></h1>
    </div>

    <?php echo $this->params->get('cancelMessage'); ?>

    <form
        name="renewCancel"
        id="renewCancel"
        action=""
        method="post">

        <input
            type="hidden"
            name="id"
            value="<?php echo $this->subscription->id; ?>"/>

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
            value="renewal.cancel"/>

        <input
            type="submit"
            value="<?php echo JText::_('COM_SIMPLERENEW_RENEWAL_CANCEL_BUTTON'); ?>"
            class="btn-main btn-big"/>

        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
