<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewPlan $this
 * @var JFormField          $field
 */

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$app = SimplerenewFactory::getApplication();
$input = $app->input;
?>
<script type="text/javascript">
    Joomla.submitbutton = function (task) {
        if (task == 'plan.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
            <?php echo $this->form->getField('description')->save(); ?>
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_simplerenew&layout=edit&id=' . (int)$this->item->id); ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="form-validate">

    <div class="form-inline form-inline-header">
        <?php
        $fields = $this->form->getFieldset('heading');
        foreach ($fields as $field) {
            echo $field->renderField();
        }
        ?>
    </div>

    <div class="form-horizontal">
        <?php
        echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'main'));

        echo $this->renderFieldset(
            'main',
            array(
                'length'       => 'unit',
                'trial_length' => 'trial_unit'
            )
        );

        echo JHtml::_(
            'bootstrap.addTab',
            'myTab',
            'description',
            JText::_('COM_SIMPLERENEW_PLAN_DESCRIPTION_LABEL')
        ); ?>
        <div class="row-fluid">
            <fieldset class="adminform">
                <?php echo $this->form->getInput('description'); ?>
            </fieldset>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
    </div>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
