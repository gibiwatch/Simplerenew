<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this;
 */

$input = SimplerenewFactory::getApplication()->input;

$action = 'index.php?option=com_simplerenew&task=subscription.create';
if ($itemid = $input->getInt('Itemid')) {
    $action .= '&Itemid=' . $itemid;
}

?>
<div class="ost-container simplerenew-subscribe">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?></h1>
    </div>

    <form action="<?php echo $action; ?>" method="post">

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
                <?php echo JHtml::_('form.token'); ?>
            </div>

        </div>
        <!-- /.ost-section -->

        <input
            id="userid"
            name="userid"
            type="hidden"
            value="<?php echo $this->user->id; ?>"/>
    </form>

</div>
<!-- /.ost-container -->
