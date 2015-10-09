<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

$heading = $this->getHeading(
    JText::plural(
        'COM_SIMPLERENEW_HEADING_RENEWAL_UPDATE',
        count($this->subscriptions),
        false
    )
);
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <?php
    if ($heading) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;

    echo SimplerenewHelper::renderModule('simplerenew_cancel_top');

    if ($support = $this->funnel->get('support')) :
        echo SimplerenewHelper::renderModule('simplerenew_cancel_support');
        echo JHtml::_(
            'link',
            JRoute::_('index.php?Itemid=' . $support),
            'Contact Support',
            'class="btn btn-main btn-small"'
        );
    endif;

    echo $this->loadTemplate('extend');
    echo $this->loadTemplate('coupon');
    echo $this->loadTemplate('cancel');

    echo SimplerenewHelper::renderModule('simplerenew_cancel_bottom');
    ?>
</div>
