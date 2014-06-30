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
<div class="ost-container ost-simplerenew-subscribe">

    <div class="page-header">
        <h1><?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?></h1>
    </div>

    <form action="<?php echo $action; ?>" method="post">

        <div class="ost-section">
            <div class="block12">
                <h3><?php echo JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION'); ?></h3>
            </div>
        </div>
        <div class="ost-section">
            <div class="block4">
                <label for="firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                <input
                    id="firstname"
                    name="firstname"
                    type="text"
                    value="<?php echo $this->user->firstname; ?>"/>
            </div>
            <div class="block4">
                <label for="lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
                <input
                    id="lastname"
                    name="lastname"
                    type="text"
                    value="<?php echo $this->user->lastname; ?>"
                    required="true"/>
            </div>
            <div class="block4">
                <label for="username"><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?></label>
                <input <?php echo $readonly; ?>
                    id="username"
                    name="username"
                    type="text"
                    value="<?php echo $this->user->username; ?>"
                    required="true"/>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section m-bottom">
             <div class="block6">
                <label for="email"><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?></label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    value="<?php echo $this->user->email; ?>"
                    required=""/>
            </div>
            <div class="block6">
                <label for="password"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    value=""/>
                <label for="password2"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD2'); ?></label>
                <input
                    id="password2"
                    name="password2"
                    type="password"
                    value=""/>
            </div>
        </div>
        <!-- /.ost-section -->

        <div class="ost-section m-bottom">
            <div class="block12">
                <?php echo $this->loadTemplate('plans'); ?>

                <?php echo $this->loadtemplate('billing'); ?>

                <div>
                    <?php echo JText::sprintf('COM_SIMPLERENEW_TERMS_OF_AGREEMENT', '#'); ?>
                </div>

                <input
                    id="userid"
                    name="userid"
                    type="hidden"
                    value="<?php echo $this->user->id; ?>"/>

                <input type="submit" value="<?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?>"/>

                <?php echo JHtml::_('form.token'); ?>

            </div>
            <!-- /.block12 -->

        </div>
        <!-- /.ost-section -->

    </form>

</div>
<!-- /.ost-container -->
