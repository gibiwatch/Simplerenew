<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

if ($support = $this->funnel->get('support')) :
    ?>
    <div class="ost-alert-notify m-bottom">
        <?php
        echo SimplerenewHelper::renderModule('simplerenew_cancel_support');

        $link = JRoute::_('index.php?Itemid=' . $support);
        $text = '<i class="fa fa-support"></i> ' . JText::_('COM_SIMPLERENEW_CONTACT_SUPPORT');
        echo JHtml::_('link', $link, $text, 'class="btn btn-main btn-small"');
        ?>
    </div>
    <?php
endif;
