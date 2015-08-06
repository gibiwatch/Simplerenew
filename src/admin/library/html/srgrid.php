<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlSrgrid
{
    /**
     * @param string $link
     * @param string $text
     * @param int    $idx
     * @param string $prefix
     * @param object $item
     * @param bool   $canCheckin
     * @param bool   $canEdit
     *
     * @return string
     */
    public static function editlink(
        $link,
        $text,
        $idx,
        $prefix,
        $item,
        $canCheckin = true,
        $canEdit = true
    ) {
        $html = '';
        if ($item->editor) {
            $html .= JHtml::_(
                'jgrid.checkedout',
                $idx,
                $item->editor,
                $item->checked_out_time,
                $prefix,
                $canCheckin
            );
        }

        if ($canEdit) {
            $html .= JHtml::_('link', $link, $text);
        } else {
            $html .= $text;
        }

        return $html;
    }
}
