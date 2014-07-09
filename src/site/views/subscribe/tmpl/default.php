<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this ;
 */

$app = SimplerenewFactory::getApplication();
?>
<div class="ost-container simplerenew-subscribe">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?></h1>
    </div>

    <form
        name="subscribeForm"
        id="subscribeForm"
        action=""
        method="post">

        <?php echo $this->loadTemplate('account'); ?>

        <div class="ost-section">

            <?php echo $this->loadTemplate('plans'); ?>

            <?php echo $this->loadtemplate('billing'); ?>

            <div class="m-bottom m-top">
                <?php echo JText::sprintf('COM_SIMPLERENEW_TERMS_OF_AGREEMENT', '#'); ?>
            </div>

            <div class="m-bottom">
                <input
                    type="submit"
                    value="<?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?>"
                    class="btn-main btn-big"/>
            </div>

        </div>
        <!-- /.ost-section -->

        <input
            type="hidden"
            name="option"
            value="com_simplerenew"/>

        <input
            type="hidden"
            name="task"
            value="subscription.create"/>

        <input
            type="hidden"
            name="Itemid"
            value="<?php echo $app->input->getInt('Itemid'); ?>"/>

        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>
<!-- /.ost-container -->
