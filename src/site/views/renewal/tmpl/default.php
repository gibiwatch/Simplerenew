<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal       $this
 * @var Simplerenew\Api\Subscription $subscription
 */
$app = SimplerenewFactory::getApplication();
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <?php
    if (!$this->subscriptions):
        echo $this->loadTemplate('nosub');
    else:
        ?>
        <?php
        if ($this->getParams()->get('show_page_heading', true)):
            ?>
            <div class="page-header">
                <h1>
                    <?php
                    echo $this->getHeading(
                        JText::plural('COM_SIMPLERENEW_RENEWAL_UPDATE', count($this->subscriptions), false)
                    ); ?>
                </h1>
            </div>
            <?php
        endif;
        ?>

        <form
            name="renewalForm"
            id="renewalForm"
            action=""
            method="post">

    <?php
        foreach ($this->subscriptions as $id => $subscription):
            $this->subscription = $subscription;
            ?>
            <div class="ost-section ost-row-two b-bottom">
                <?php echo $this->loadTemplate('plan'); ?>
            </div>
            <?php
        endforeach; ?>
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
                value="renewal.update"/>

            <button type="submit" class="btn-main btn-big m-top">
                <i class="fa fa-check"></i>
                <?php echo JText::_('COM_SIMPLERENEW_RENEWAL_UPDATE_BUTTON'); ?>
            </button>

            <?php echo JHtml::_('form.token'); ?>
        </form>
    <?php
    endif;
    ?>
</div>
