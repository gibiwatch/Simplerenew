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
     * @var string
     */
    protected static $defaultIconColor = '#333';

    /**
     * Add a custom link accommodating J25/J3x differences
     *
     * @param string $task
     * @param string $icon
     * @param string $iconOver
     * @param string $alt
     * @param bool   $listSelect
     * @param string $iconColor
     *
     * @return void
     */
    public static function custom(
        $task = '',
        $icon = '',
        $iconOver = '',
        $alt = '',
        $listSelect = true,
        $iconColor = null
    ) {
        static::setIcon($icon, $iconColor);
        parent::custom($task, $icon, $iconOver, $alt, $listSelect);
    }

    /**
     * Add a simple link to the toolbar.
     *
     * @param string $href
     * @param string $alt
     * @param string $icon
     * @param string $iconColor
     */
    public static function link($href, $alt, $icon = 'link', $iconColor = null)
    {
        $bar = JToolbar::getInstance('toolbar');

        static::setIcon($icon, $iconColor);
        $bar->appendButton('Link', $icon, $alt, $href);
    }

    /**
     * Add a simple link to a view
     *
     * @param string $view
     * @param string $alt
     * @param string $icon
     * @param string $iconColor
     * @param string $option
     */
    public static function view($view, $alt, $icon = 'view', $iconColor = null, $option = null)
    {
        $option = $option ?: static::getOption();
        $href   = "index.php?option={$option}&view={$view}";
        static::setIcon($icon, $iconColor, $option);
        static::link($href, $alt, $icon);
    }

    /**
     * Accept an icon name adding css needed for J2X/J3X. $icon is
     * considered to be a short name that will translate to a file
     * named 'icon-32-{$icon}.png'. If passed in the form 'mediapath/name'
     * $option will be overridden by what will be assumed is a relative path.
     * Note that $option is ignored in Jommla! >= v3.0
     *
     * @param string $icon
     * @param string $color
     * @param string $option
     *
     * @return void
     */
    protected static function setIcon($icon, $color = null, $option = null)
    {
        if (strpos($icon, '/') > 0) {
            list($option, $icon) = explode('/', $icon, 2);
        }
        $option = $option ?: static::getOption();

        $img = JHtml::_('image', "{$option}/icon-32-{$icon}.png", null, null, true, true);
        if ($img) {
            $doc = SimplerenewFactory::getDocument();

            if (version_compare(JVERSION, '3', 'lt')) {
                $doc->addStyleDeclaration(
                    ".icon-32-{$icon} { background-image: url({$img}); background-repeat: no-repeat; }"
                );

            } else {
                $color = $color ?: static::$defaultIconColor;
                $doc->addStyleDeclaration(".icon-{$icon}:before { color: {$color}; }");
            }
        }
    }

    /**
     * @return string
     */
    protected static function getOption()
    {
        return SimplerenewFactory::getApplication()->input->getCmd('option', 'com_simplerenew');
    }
}
