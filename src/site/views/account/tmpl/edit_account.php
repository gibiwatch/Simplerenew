<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewAccount $this
 */

if ($this->getParams()->get('editAccount')) :
    $heading = JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION');
    echo $this->stepHeading($heading);

    ?>
    <div class="ost-section">
        <div class="block6">
            <label for="firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?> <span>*</span></label>
            <input
                id="firstname"
                name="firstname"
                type="text"
                value="<?php echo $this->escape($this->user->firstname); ?>"
                maxlength="50"
                required="true"
                autofocus/>
        </div>
        <div class="block6">
            <label for="lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?> <span>*</span></label>
            <input
                id="lastname"
                name="lastname"
                type="text"
                maxlength="50"
                value="<?php echo $this->escape($this->user->lastname); ?>"
                required="true"/>
        </div>
    </div>
    <!-- /.ost-section -->

    <div class="ost-section">
        <div class="block6">
            <label for="username"><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?> <span>*</span></label>
            <input
                id="username"
                name="username"
                type="text"
                maxlength="50"
                value="<?php echo $this->escape($this->user->username); ?>"
                readonly/>
        </div>
        <div class="block6">
            <label for="email"><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?> <span>*</span></label>
            <input
                id="email"
                name="email"
                type="email"
                maxlength="50"
                value="<?php echo $this->escape($this->user->email); ?>"
                required="true"
                class="unique_email"/>
        </div>
    </div>
    <div class="ost-section p-bottom b-bottom">
        <div class="block6">
            <label for="password"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?></label>
            <input
                id="password"
                name="password"
                type="password"
                maxlength="100"
                value=""/>
        </div>
        <div class="block6">
            <label for="password2"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD2'); ?></label>
            <input
                id="password2"
                name="password2"
                type="password"
                maxlength="100"
                value=""/>
        </div>
    </div>
    <!-- /.ost-section -->
    <?php
else :
    ?>
    <input type="hidden" name="firstname" value="<?php echo $this->user->firstname; ?>"/>
    <input type="hidden" name="lastname" value="<?php echo $this->user->lastname; ?>"/>
    <input type="hidden" name="username" value="<?php echo $this->user->username; ?>"/>
    <input type="hidden" name="email" value="<?php echo $this->user->email; ?>"/>
    <?php
endif;
