<?php
/**
 * @package   mod_osmyaccount
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

//load css
JHtml::_('stylesheet', 'modules/mod_osmyaccount/assets/css/style.css');

?>
<div class="osmyaccountbox">
    <div class="osmyaccountbox_thumb">
        <?php echo $helper->getAvatar('style="float: left;"'); ?>
        <div class="osmyaccountbox_info">
            <div class="osmyaccountbox_name"><?php echo $account->first_name . ' ' . $account->last_name; ?></div>
            <div class="osmyaccountbox_place">
                <?php echo $account->username; ?><br/>
                <?php echo $account->company_name; ?>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="osmyaccountbox_membership">
        <?php echo JHtml::_('subscriber.banner'); ?><br/>
        <?php echo JHtml::_('subscriber.status'); ?>
    </div>
    <?php echo JHtml::_(
        'subscriber.button',
        $subscription,
        'class="recurly_bg_btn recurly_subscribeonly"',
        'resubscribe'
    ); ?>
</div>
