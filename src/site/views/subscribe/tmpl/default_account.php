<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

$readonly = '';
if ($this->user->id > 0) {
    $readonly = ' readonly="true"';
}

echo $this->stepHeading(JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION'));
?>
<div class="ost-section">
    <div class="block6">
        <label for="firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?> <span>*</span></label>
        <input
            id="firstname"
            name="firstname"
            type="text"
            value="<?php echo $this->user->firstname; ?>"
            required="true"
            autofocus/>
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
            type="email"
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
