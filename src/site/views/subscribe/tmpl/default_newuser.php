<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="page-header">
    <h1><?php echo JText::_('COM_SIMPLERENEW_SUBSCRIBE'); ?></h1>
</div>

<?php echo $this->loadTemplate('account'); ?>

<div class="ost-section">

    <?php echo $this->loadTemplate('plans'); ?>

    <?php echo $this->loadtemplate('billing'); ?>

    <div class="m-bottom m-top">
        <?php echo JHtml::_('sr.terms'); ?>
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
    name="task"
    value="subscription.create"/>
