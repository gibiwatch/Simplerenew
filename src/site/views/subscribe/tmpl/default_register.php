<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

$loggedIn    = ($this->user->id > 0);
?>
<div class="ost-section">
    <div class="block6">
        <label for="firstname">
            <?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            id="firstname"
            name="firstname"
            type="text"
            value="<?php echo $this->escape($this->user->firstname); ?>"
            required="true"
            maxlength="50"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_FIRSTNAME_REQUIRED'); ?>"
            autofocus/>
    </div>
    <div class="block6">
        <label for="lastname">
            <?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            id="lastname"
            name="lastname"
            type="text"
            value="<?php echo $this->escape($this->user->lastname); ?>"
            maxlength="50"
            required="true"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_LASTNAME_REQUIRED'); ?>"/>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section">
    <div class="block6">
        <label for="username">
            <?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            <?php
            if ($loggedIn) {
                echo 'readonly';
            }
            ?>
            id="username"
            name="username"
            class="unique_user"
            type="text"
            value="<?php echo $this->escape($this->user->username); ?>"
            maxlength="50"
            required="true"
            data-include="#email"
            data-recheck="#email #password"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_REQUIRED'); ?>"/>
    </div>
    <div class="block6">
        <label for="email">
            <?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            id="email"
            name="email"
            type="email"
            class="unique_email"
            value="<?php echo $this->escape($this->user->email); ?>"
            maxlength="50"
            required="true"
            data-include="#username"
            data-recheck="#username #password"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_REQUIRED'); ?>"
            data-msg-email="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_EMAIL_FORMAT'); ?>"/>
    </div>
</div>

<?php if (!$loggedIn): ?>
    <div class="ost-section">
        <div class="block6">
            <label for="password">
                <?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?>
                <?php echo $this->requiredTag; ?>
            </label>
            <input
                id="password"
                name="password"
                type="password"
                class="verify_password"
                value=""
                maxlength="100"
                required="true"
                data-include="#username #email"
                data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_REQUIRED'); ?>"/>
        </div>
        <div class="block6">
            <label for="password2">
                <?php echo JText::_('COM_SIMPLERENEW_PASSWORD2'); ?>
                <?php echo $this->requiredTag; ?>
            </label>
            <input
                id="password2"
                name="password2"
                type="password"
                value=""
                maxlength="100"
                required="true"
                data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD2_REQUIRED'); ?>"
                data-msg-equalto="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD2_MISMATCH'); ?>"/>
        </div>
    </div>
<?php endif; ?>
<!-- /.ost-section -->
<!-- .simplerenew-subscribe-user -->
