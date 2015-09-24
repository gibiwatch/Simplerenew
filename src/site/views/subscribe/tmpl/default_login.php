<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
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
            type="text"
            value="<?php echo $this->user->username; ?>"
            maxlength="50"
            required="true"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_USERNAME_REQUIRED'); ?>"/>
    </div>
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
            required="true"
            data-msg-required="<?php echo JText::_('COM_SIMPLERENEW_VALIDATE_PASSWORD_REQUIRED'); ?>"/>
    </div>
</div>
