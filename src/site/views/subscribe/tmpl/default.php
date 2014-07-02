<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$input = SimplerenewFactory::getApplication()->input;

$action = 'index.php?option=com_simplerenew&task=subscription.create';
if ($itemid = $input->getInt('Itemid')) {
    $action .= '&Itemid=' . $itemid;
}

$readonly = '';
if ($this->user->id > 0) {
    $readonly = ' readonly="true"';
}
?>
<div class="ost-container simplerenew-subscribe">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?></h1>
    </div>

    <form action="<?php echo $action; ?>" method="post">

        <h3><span><?php echo JText::_('COM_SIMPLERENEW_HEADING_STEP1'); ?></span> <?php echo JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION'); ?></h3>

        <div class="ost-section">
            <div class="block6">
                <label for="firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?> <span>*</span></label>
                <input
                    id="firstname"
                    name="firstname"
                    type="text"
                    value="<?php echo $this->user->firstname; ?>"
                    equired="true"/>
            </div>
            <div class="block6">
                <label for="lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?> <span>*</span></label>
                <input
                    id="lastname"
                    name="lastname"
                    type="text"
                    value="<?php echo $this->user->lastname; ?>"
                    required="true"/>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section">
            <div class="block6">
                <label for="username"><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?> <span>*</span></label>
                <input <?php echo $readonly; ?>
                    id="username"
                    name="username"
                    type="text"
                    value="<?php echo $this->user->username; ?>"
                    required="true"/>
            </div>
            <div class="block6">
                <label for="email"><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?> <span>*</span></label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    value="<?php echo $this->user->email; ?>"
                    required=""/>
            </div>
        </div>
        <div class="ost-section p-bottom b-bottom">
            <div class="block6">
                <label for="password"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?> <span>*</span></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    value=""
                    required="true"/>
            </div>
            <div class="block6">
                <label for="password2"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD2'); ?> <span>*</span></label>
                <input
                    id="password2"
                    name="password2"
                    type="password"
                    value=""
                    required="true"/>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section">

            <?php echo $this->loadTemplate('plans'); ?>

            <?php echo $this->loadtemplate('billing'); ?>

            <div class="m-bottom">
                <?php echo JText::sprintf('COM_SIMPLERENEW_TERMS_OF_AGREEMENT', '#'); ?>
            </div>

            <div class="m-bottom">
                <input
                    type="submit"
                    value="<?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?>"
                    class="btn-main"/>
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
