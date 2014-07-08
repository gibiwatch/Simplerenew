<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewAccount $this
 */
$app = SimplerenewFactory::getApplication();

?>
<form
    action="index.php"
    method="post"
    name="item-form"
    id="item-form"
    class="form-validate">

    <?php echo $this->loadTemplate('account'); ?>

    <?php echo $this->loadTemplate('billing'); ?>

    <input
        type="hidden"
        name="id"
        value="<?php echo $this->user->id; ?>"/>

    <input
        type="hidden"
        name="option"
        value="com_simplerenew"/>
    <input
        type="hidden"
        name="Itemid"
        value="<?php echo $app->input->getInt('Itemid'); ?>"/>

    <input
        type="hidden"
        name="task"
        value="account.save"/>

    <input type="submit" value="Save"/>

    <?php echo JHtml::_('form.token'); ?>
</form>
