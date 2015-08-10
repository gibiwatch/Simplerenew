<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewToolbarHelper extends JToolbarHelper
{
    /**
     * Add a custom link accommodating J25/J3x differences
     *
     * @param string $task
     * @param string $icon
     * @param string $iconOver
     * @param string $alt
     * @param bool   $listSelect
     * @param string $iconColor
     */
    public static function custom(
        $task = '',
        $icon = '',
        $iconOver = '',
        $alt = '',
        $listSelect = true,
        $iconColor = '#333'
    ) {
        $img = JHtml::_('image', "com_simplerenew/icon-32-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = SimplerenewFactory::getDocument();

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $doc->addStyleDeclaration(
                    ".icon-32-{$icon} { background-image: url({$img}); background-repeat: no-repeat; }"
                );

            } else {
                $doc->addStyleDeclaration(".icon-{$icon}:before { color: {$iconColor}; }");
            }
        }
        parent::custom($task, $icon, $iconOver, $alt, $listSelect);
    }

    /**
     * Add a simple link to the toolbar.
     *
     * @param string $href
     * @param string $alt
     * @param string $icon
     */
    public static function link($href, $alt, $icon)
    {
        $bar = JToolbar::getInstance('toolbar');

        // Add a raw link button.
        $bar->appendButton('Link', $icon, $alt, $href);
    }

}
