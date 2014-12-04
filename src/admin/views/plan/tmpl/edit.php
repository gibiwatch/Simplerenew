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
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet('com_simplerenew/admin.css', null, true);

$app = SimplerenewFactory::getApplication();
$input = $app->input;

?>
<script type="text/javascript">
    Joomla.submitbutton = function (task) {
        if (task == 'plan.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }
</script>

<div id="ost-custom">
    <form
        action="<?php echo JRoute::_('index.php?option=com_simplerenew&layout=edit&id=' . (int)$this->item->id); ?>"
        method="post"
        name="adminForm"
        id="item-form"
        class="form-validate">

        <?php
        foreach ($this->form->getFieldset('main') as $field) {
            echo $field->label;
        }
        ?>

        <p><?php
            $code = $this->form->getValue('code');
            $lang = $code ? 'COM_SIMPLERENEW_NARRATIVE_PLAN_NAMECODE' : 'COM_SIMPLERENEW_NARRATIVE_PLAN_NAME';
            echo JText::sprintf($lang, $this->form->getInput('name'), $code);
            ?>
        </p>

        <p>
            <?php echo JText::sprintf('COM_SIMPLERENEW_NARRATIVE_PLAN_GROUP', $this->form->getInput('group_id')); ?>
        </p>

        <p>
            <?php
            echo JText::sprintf(
                'COM_SIMPLERENEW_NARRATIVE_PLAN_FEES',
                $this->form->getInput('amount'),
                $this->form->getInput('length'),
                $this->form->getInput('unit'),
                $this->form->getInput('trial_length'),
                $this->form->getInput('trial_unit')
            );
            ?>
        </p>

        <p>
            <?php echo JText::sprintf('COM_SIMPLERENEW_NARRATIVE_PLAN_SETUP', $this->form->getInput('setup_cost')); ?>
        </p>

        <p>
            <?php echo JText::sprintf('COM_SIMPLERENEW_NARRATIVE_PLAN_CURRENCY', $this->form->getValue('currency')); ?>
        </p>

        <p>
            <?php echo JText::sprintf('COM_SIMPLERENEW_NARRATIVE_PLAN_STATE', $this->form->getInput('published')); ?>
        </p>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
