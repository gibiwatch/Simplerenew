<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewToolbarHelper extends JToolbarHelper
{
    public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
    {
        $img = JHtml::_('image', "com_simplerenew/icon-32-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = JFactory::getDocument();
            $doc->addStyleDeclaration(".icon-32-{$icon} { background-image: url({$img}); background-repeat: no-repeat; }");
            $doc->addStyleDeclaration(".icon-{$icon} { background-image: url({$img}); background-repeat: no-repeat; background-size: 16px auto; }");
        }
        parent::custom($task, $icon, $iconOver, $alt, $listSelect);
    }
}
