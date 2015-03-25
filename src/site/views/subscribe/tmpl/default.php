<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this ;
 */

JHtml::_('sr.validation.init', '#subscribeForm');

$app = SimplerenewFactory::getApplication();
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-subscribe'); ?>">

    <form
        name="subscribeForm"
        id="subscribeForm"
        action=""
        method="post"
        data-popup-warning="<?php echo JText::sprintf('COM_SIMPLERENEW_WARN_PAYPAL_POPUP_FAIL', JUri::base()); ?>">

        <?php
        if ($this->allowMultiple || !count($this->subscriptions)) {
            echo $this->loadTemplate('newuser');
        } else {
            echo $this->loadTemplate('changeplan');
        }
        ?>

        <input
            type="hidden"
            name="option"
            value="com_simplerenew"/>

        <input
            type="hidden"
            name="Itemid"
            value="<?php echo $app->input->getInt('Itemid'); ?>"/>

        <span id="token">
            <?php echo JHtml::_('form.token'); ?>
        </span>
    </form>

</div>
<!-- /.ost-container -->
