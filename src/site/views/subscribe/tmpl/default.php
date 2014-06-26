<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<div class="ost-container">
    <form>
        <ul>
            <li>
                <label><?php echo JText::_('COM_SIMPLERENEW_FIRSTNAME'); ?></label>
                <input type="text"name="firstname" value=""<?php echo $this->user->firstname; ?>"/>
            </li>

        </ul>

    </form>
</div>
