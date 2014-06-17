<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

//$this->hiddenFieldsets = array();
//$this->hiddenFieldsets[0] = 'basic-limited';
//$this->configFieldsets = array();
//$this->configFieldsets[0] = 'editorConfig';

// Create shortcut to parameters.
//$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'plan.cancel' || document.formvalidator.isValid(document.id('item-form')))
        {
            <?php //echo $this->form->getField('articletext')->save(); ?>
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_simplerenew&layout=edit&id=' . (int) @$this->item->id); ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="form-validate">

    <?php //echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span9">
                <fieldset class="adminform">
                    main part<?php //echo $this->form->getInput('articletext'); ?>
                </fieldset>
            </div>
            <div class="span3">
                side part<?php //echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
        </div>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
