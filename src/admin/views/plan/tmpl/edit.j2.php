<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<form
    action="<?php echo 'index.php?option=com_simplerenew&view=plan&layout=edit&id=' . (int)@$this->item->id; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="form-validate">

    <div class="width-50 fltlft">
        <fieldset class="adminform">
            <legend>
                Joomla 2.x not yet supported
            </legend>

            <ul class="adminformlist">
                <li></li>

            </ul>
        </fieldset>
    </div>

    <input
        type="hidden"
        name="task"
        value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>
