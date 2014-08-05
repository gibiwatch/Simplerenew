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
JHtml::_('behavior.tooltip');

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

    <?php
    echo $this->renderFieldset(
        'main',
        array(
            'length'       => 'unit',
            'trial_length' => 'trial_unit'
        )
    );
    ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
