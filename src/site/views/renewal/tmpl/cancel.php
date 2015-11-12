<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;
use Simplerenew\DateTime as SRDateTime;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */
?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-renewal'); ?>">
    <div class="page-header">
        <h1><?php
            echo JText::plural('COM_SIMPLERENEW_HEADING_RENEWAL_CANCEL', count($this->subscriptions), false);
            ?></h1>
    </div>
    <?php

    echo SimplerenewHelper::renderModule('simplerenew_cancel_top');
    echo $this->loadTemplate('support');
    echo $this->loadTemplate('extend');
    echo $this->loadTemplate('coupon');
    echo $this->loadTemplate('cancel');

    echo SimplerenewHelper::renderModule('simplerenew_cancel_bottom');
    ?>
</div>
