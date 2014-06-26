<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelperSite
{
    protected static $awesomeCDN = 'https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css';

    protected static $defaultFont = 'Open Sans:400,300,400italic,600,700';

    /**
     * @var JRegistry
     */
    protected static $params = null;

    /**
     * @return JRegistry
     */
    public static function getParams()
    {
        if (self::$params === null) {
            self::$params = SimplerenewComponentHelper::getParams('com_simplerenew');
        }
        return self::$params;
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
        $params = self::getParams();

        // Load the selected font
        $font     = $params->get('advanced.fontFamily', self::$defaultFont);
        $loadFont = ($font != 'none');

        if ($loadFont) {
            $href = 'http://fonts.googleapis.com/css?family=' . $font;
            JHtml::stylesheet($href);
        }

        // Load font Awesome
        switch ($params->get('advanced.fontAwesome', 'local')) {
            case 'local':
                JHtml::stylesheet('com_simplerenew/awesome/css/font-awesome.min.css', null, true);
                break;

            case 'cdn':
                JHtml::stylesheet(self::$awesomeCDN);
                break;
        }

        // Load the selected theme
        if ($theme === null) {
            $theme = $params->get('advanced.theme', 'default.css');
        }
        if ($theme != 'none') {
            JHtml::stylesheet('com_simplerenew/grid.css', null, true);
            JHtml::stylesheet('com_simplerenew/grid-responsive.css', null, true);
            JHtml::stylesheet('com_simplerenew/themes/' . $theme, null, true);
        }
    }
}

/*

	<!-- elements -->
	<link href="css/styles.css" rel="stylesheet">

	<!-- grid -->
	<link href="css/grid.css" rel="stylesheet">
	<link href="css/grid-responsive.css" rel="stylesheet">
 */
