<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlSr
{
    public static function terms()
    {
        $params = SimplerenewComponentHelper::getParams();

        if ($itemid = $params->get('basic.terms')) {
            $link = JHtml::_(
                'link',
                JRoute::_('index.php?Itemid=' . $itemid),
                JText::_('COM_SIMPLERENEW_TERMS_OF_AGREEMENT_LINK_TEXT'),
                'target="_blank"'
            );

            return JText::sprintf('COM_SIMPLERENEW_TERMS_OF_AGREEMENT', $link);
        }
        return '';
    }
}
