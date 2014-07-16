<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

abstract class JHtmlSrselect
{
    /**
     * Create a Credit Card expiration year dropdown
     *
     * @param string $name
     * @param mixed  $attribs
     * @param mixed  $selected
     * @param mixed  $idtag
     * @param bool   $translate
     *
     * @return mixed
     */
    public static function ccyear($name, $attribs = null, $selected = null, $idtag = false, $translate = false)
    {
        $now  = date('Y');
        $data = array();

        for ($i = $now; $i < ($now + 12); $i++) {
            $data[] = JHtml::_('select.option', $i, $i);
        }

        return JHtml::_('select.genericlist', $data, $name, $attribs, 'value', 'text', $selected, $idtag, $translate);
    }

    /**
     * Create a Credit Card expiration month dropdown
     *
     * @param string $name
     * @param mixed  $attribs
     * @param string $selected
     * @param mixed  $idtag
     * @param bool   $translate
     *
     * @return string
     */
    public static function ccmonth($name, $attribs = null, $selected = null, $idtag = false, $translate = false)
    {
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $date   = date('F', mktime(0, 0, 0, $i, 1));
            $data[] = JHtml::_('select.option', $i, $i . ' - ' . $date);
        }

        return JHtml::_('select.genericlist', $data, $name, $attribs, 'value', 'text', $selected, $idtag, $translate);
    }

    public static function country($name, $attribs = null, $selected = null, $idtag = false, $translate = false)
    {
        if ($attribs && !is_array($attribs)) {
            $attribs = JUtility::parseAttributes($attribs);
        }
        $attribs['name'] = $name;
        $attribs['id'] = $idtag ? : preg_replace('/(\[\]|\[|\])/', '_', $name);
        $attribs['type'] = 'text';
        $attribs['value'] = $selected;
        $html = '<input ' . JArrayHelper::toString($attribs) . '/>';

        return $html;
    }
}
