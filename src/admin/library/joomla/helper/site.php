<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelperSite
{
    protected static $googleFonts = 'https://fonts.googleapis.com';

    /**
     * @var JRegistry
     */
    protected static $params = null;

    /**
     * @return JRegistry
     */
    public static function getParams()
    {
        if (static::$params === null) {
            static::$params = SimplerenewComponentHelper::getParams('com_simplerenew');
        }
        return static::$params;
    }

    /**
     * Load all theming/styling items
     * - Awesome Icon font
     * - Google Font
     * - Selected Theme
     *
     * @param null $theme
     */
    public static function loadTheme($theme = null)
    {
        $params = static::getParams();

        // Load the selected font
        $font = explode('|', $params->get('themes.fontFamily', 'none'));
        if ($font[0] != 'none') {
            /* Load Google fonts files when font-weight exists
            *  Example: "Droid Sans|sans-serif|400,700"
            *  400,700 is the font-weight */
            if (count($font) > 2) {
                $href = static::$googleFonts . '/css?family=' . $font[0] . ':' . $font[2];
                JHtml::_('stylesheet', $href);
            }

            // Assign font-family to specific tags
            $style = array(
                '.ost-container p,',
                '.ost-container h1,',
                '.ost-container h2,',
                '.ost-container h3,',
                '.ost-container div,',
                '.ost-container li,',
                '.ost-container span,',
                '.ost-container label,',
                '.ost-container td,',
                '.ost-container input,',
                '.ost-container button,',
                '.ost-container textarea,',
                '.ost-container select {',
                "   font-family: '" . $font[0] . "', " . $font[1] . ';',
                '}'
            );
            JFactory::getDocument()->addStyleDeclaration(join("\n", $style));
        }

        // Load font Awesome
        if ($params->get('themes.fontAwesome', true)) {
            JHtml::_('stylesheet', 'com_simplerenew/awesome/css/font-awesome.min.css', null, true);
        }

        // Load responsive grids
        JHtml::_('stylesheet', 'com_simplerenew/grid.css', null, true);
        JHtml::_('stylesheet', 'com_simplerenew/grid-responsive.css', null, true);
        JHtml::_('stylesheet', 'com_simplerenew/style.css', null, true);

        // Load the selected theme
        if ($theme === null) {
            $theme = $params->get('themes.theme', 'default.css');
        }
        if ($theme != 'none') {
            JHtml::_('stylesheet', 'com_simplerenew/themes/' . $theme, null, true);
        }
    }
}
