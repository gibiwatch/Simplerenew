<?php
/**
 * @package    Simplerenew
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014-2015 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JhtmlSrlink
{
    /**
     * Generate correct link to configuration options
     *
     * @param null  $text
     * @param mixed $attribs
     *
     * @return string
     */
    public static function options($text = null, $attribs = array())
    {
        if (is_string($attribs)) {
            $attribs = JUtility::parseAttributes($attribs);
        } elseif (!is_array($attribs)) {
            $attribs = array();
        }

        $link = 'index.php?option=com_config&view=component&component=com_simplerenew';
        if (version_compare(JVERSION, '3.0', 'lt')) {
            JHtml::_('behavior.modal');

            if (!isset($attribs['class'])) {
                $attribs['class'] = '';
            }
            $attribs['class'] .= ' modal';
            $attribs['rel'] = "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}";
            $link .= '&tmpl=component';
        }

        $text = JText::_($text ?: 'COM_SIMPLERENEW_OPTIONS');
        return JHtml::_('link', $link, $text, $attribs);
    }
}
