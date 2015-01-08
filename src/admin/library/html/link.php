<?php
/**
 * @package    Simplerenew
 * @subpackage
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlLink
{
    public static function options($text = null, $attribs = array())
    {
        if (is_string($attribs)) {
            $attribs = JUtility::parseAttributes($attribs);
        }
        if (empty($attribs['class'])) {
            $attribs['class'] = 'btn btn-small';
        }

        $link = 'index.php?option=com_config&view=component&component=com_simplerenew';
        if (version_compare(JVERSION, '3.0', 'lt')) {
            JHtml::_('behavior.modal');
            $attribs['class'] .= ' modal';
            $attribs['rel'] = "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}";
            $link .= '&tmpl=component';

        }

        return JHtml::_('link', $link, $text ?: 'Options', $attribs);
    }
}
