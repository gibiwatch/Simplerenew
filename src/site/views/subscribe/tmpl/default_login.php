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
SimplerenewFactory::getLanguage()->load('com_users');

?>
<div class="ost-section">
    <div class="block6">
        <label for="username">
            <?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            id="usernameLogin"
            name="usernameLogin"
            type="text"
            value="<?php echo $this->escape($this->user->username); ?>"
            maxlength="50"
            required="true"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_REQUIRED'); ?>"/>

        <div class="m-bottom">
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
                <?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?>
            </a>
        </div>
    </div>
    <div class="block6">
        <label for="password">
            <?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?>
            <?php echo $this->requiredTag; ?>
        </label>
        <input
            id="passwordLogin"
            name="passwordLogin"
            type="password"
            value=""
            maxlength="100"
            required="true"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_REQUIRED'); ?>"/>

        <div class="m-bottom">
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                <?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
            </a>
        </div>
    </div>
</div>
