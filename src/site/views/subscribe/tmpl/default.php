<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$readonly = '';
if ($this->user->id > 0) {
    $readonly = ' readonly="true"';
}
?>
<div class="ost-container">
    <form>
        <ul>
            <li>
                <label for="firstname"><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                <input
                    id="firstname"
                    name="firstname"
                    type="text"
                    value="<?php echo $this->user->firstname; ?>"/>
            </li>

            <li>
                <label for="lastname"><?php echo JText::_('COM_SIMPLERENEW_LASTNAME'); ?></label>
                <input
                    id="lastname"
                    name="lastname"
                    type="text"
                    value="<?php echo $this->user->lastname; ?>"
                    required="true"/>
            </li>

            <li>
                <label for="username"><?php echo JText::_('COM_SIMPLERENEW_USERNAME'); ?></label>
                <input <?php echo $readonly; ?>
                    id="username"
                    name="username"
                    type="text"
                    value="<?php echo $this->user->username; ?>"
                    required="true"/>
            </li>

            <li>
                <label for="email"><?php echo JText::_('COM_SIMPLERENEW_EMAIL'); ?></label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    value="<?php echo $this->user->email; ?>"
                    required=""/>
            </li>

            <li>
                <label for="password"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD'); ?></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    value=""/>
            </li>

            <li>
                <label for="password2"><?php echo JText::_('COM_SIMPLERENEW_PASSWORD2'); ?></label>
                <input
                    id="password2"
                    name="password2"
                    type="password"
                    value=""/>
            </li>
        </ul>

        <?php echo $this->loadTemplate('plans'); ?>

        <?php echo $this->loadtemplate('billing'); ?>

        <div>
            <?php echo JText::sprintf('COM_SIMPLERENEW_TERMS_OF_AGREEMENT', '#'); ?>
        </div>

        <input
            id="user_id"
            name="user_id"
            type="hidden"
            value="<?php echo $this->user->id; ?>"/>
    </form>
</div>
