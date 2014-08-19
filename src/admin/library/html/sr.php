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
    protected static $jqueryLoaded = array();

    /**
     * Generate link to Terms & Conditions page
     *
     * @return string
     */
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

    /**
     * Load jQuery core
     *
     * @param bool $noConflict
     * @param bool $debug
     */
    public static function jquery($noConflict = true, $debug = null)
    {
        $params = SimplerenewComponentHelper::getParams();

        if ($params->get('advanced.jquery', 1)) {
            // Only load once
            if (!empty(static::$jqueryLoaded[__METHOD__])) {
                return;
            }

            if (version_compare(JVERSION, '3', 'ge')) {
                JHtml::_('jquery.framework', $noConflict, $debug);
            } else {
                // pre 3.0 manual loading

                // If no debugging value is set, use the configuration setting
                if ($debug === null) {
                    $config = JFactory::getConfig();
                    $debug  = (boolean)$config->get('debug');
                }

                JHtml::_('script', 'com_simplerenew/jquery.js', false, true, false, false, $debug);

                // Check if we are loading in noConflict
                if ($noConflict) {
                    JHtml::_('script', 'com_simplerenew/jquery-noconflict.js', false, true, false, false, false);
                }
            }
        }

        static::$jqueryLoaded[__METHOD__] = true;
    }

    /**
     * Setup tabbed areas
     *
     * @param $selector
     *
     * @return void
     */
    public static function tabs($selector)
    {
        static::jquery();
        JHtml::_('script', 'com_simplerenew/utilities.js', false, true);

        $js = array(
            "(function($) {",
            "   $(document).ready(function () {",
            "       $.Simplerenew.tabs('{$selector}');",
            "    });",
            "})(jQuery);"
        );
        SimplerenewFactory::getDocument()->addScriptDeclaration(join("\n", $js));
    }

    /**
     * Setup simple sliders
     *
     * @param string $selector
     * @param bool   $visible
     *
     * @return void
     */
    public static function sliders($selector, $visible = false)
    {
        static::jquery();
        JHtml::_('script', 'com_simplerenew/utilities.js', false, true);

        $options = json_encode(
            array(
                'visible' => (bool)$visible
            )
        );

        $js = array(
            "(function($) {",
            "   $(document).ready(function () {",
            "      $.Simplerenew.sliders('{$selector}', {$options});",
            "   });",
            "})(jQuery);"
        );
        SimplerenewFactory::getDocument()->addScriptDeclaration(join("\n", $js));
    }

    /**
     * Load js support for jQuery form validation
     */
    public static function validation()
    {
        static::jquery();

        JHtml::_('script', 'com_simplerenew/validation/jquery.validate.js', false, true);
    }
}
