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
$fieldSets = $this->form->getFieldsets();

$style = <<<CSS
div.inline label {
    clear: none;
    min-width: 0px;
}
CSS;
SimplerenewFactory::getDocument()->addStyleDeclaration($style);

?>
<script>
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

    <div class="width-100 inline">
        <fieldset class="adminform fltlft">
            <?php
            echo $this->form->getLabel('code');
            echo $this->form->getInput('code');
            echo $this->form->getLabel('alias');
            echo $this->form->getInput('alias');
            ?>
        </fieldset>
    </div>
    <div class="clr"></div>

    <?php echo JHtml::_('tabs.start', 'plans-pane'); ?>

    <?php
    echo JHtml::_(
        'tabs.panel',
        JText::_($fieldSets['main']->label),
        $fieldSets['main']->name . '-page'
    );
    ?>
    <div class="width-100">
        <fieldset class="adminform fltlft">
            <ul class="adminformlist">
                <?php
                $mainFields = $this->form->getFieldset('main');
                foreach ($mainFields as $field) {
                    echo '<li>' . $field->label . $field->input . '</li>';
                }
                ?>
            </ul>
        </fieldset>
    </div>
    <div class="clr"></div>

    <?php
    echo JHtml::_(
        'tabs.panel',
        JText::_('COM_SIMPLERENEW_PLAN_DESCRIPTION_LABEL'),
        'description-page'
    );
    ?>
    <div class="width-100">
        <fieldset class="adminform">
            <?php echo $this->form->getInput('description'); ?>
        </fieldset>
    </div>
    <div class="clr"></div>

    <?php echo JHtml::_('tabs.end'); ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
