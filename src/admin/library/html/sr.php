<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlSr
{
    protected static $utilitiesLoaded = false;

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
     * @param bool $utilities
     * @param bool $noConflict
     * @param bool $debug
     */
    public static function jquery($utilities = true, $noConflict = true, $debug = null)
    {
        $app    = SimplerenewFactory::getApplication();
        $params = SimplerenewComponentHelper::getParams();

        $load     = $params->get('advanced.jquery', 1);
        $client   = $app->getName();
        if ($load == $client || $load == 1) {
            $jqueryLoaded = $app->get('jquery', false);
            // Only load once
            if (!$jqueryLoaded) {
                if (version_compare(JVERSION, '3.0', 'lt')) {
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
                } else {
                    JHtml::_('jquery.framework', $noConflict, $debug);
                }
            }
            $app->set('jquery', true);
        }

        if ($utilities && !static::$utilitiesLoaded) {
            JHtml::_('script', 'com_simplerenew/utilities.js', false, true);
            static::$utilitiesLoaded = true;
        }
    }

    /**
     * Setup tabbed areas
     *
     * @param string       $selector jQuery selector for tab headers
     * @param array|string $options  Associative array or JSON string of tabber options
     *
     * @return void
     */
    public static function tabs($selector, $options = null)
    {
        static::jquery();

        if ($options && is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = array();
        }
        $options['selector'] = $selector;

        $options = json_encode($options);
        static::onready("$.Simplerenew.tabs({$options});");
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

        $options = json_encode(
            array(
                'selector' => $selector,
                'visible'  => (bool)$visible
            )
        );

        static::onready("$.Simplerenew.sliders({$options});");
    }

    /**
     * Setup collection of toggle panels
     *
     * @param string     $selector
     * @param int|string $visible
     */
    public static function toggles($selector, $visible = 0)
    {
        $options = json_encode(
            array(
                'selector' => $selector,
                'current'  => $visible
            )
        );
        static::onready("$.Simplerenew.toggles({$options});");
    }

    /**
     * Create a clickable area for radio buttons and checkboxes.
     * Will accept a string as the jQuery selector for areas or
     * more detailed options as either json string or an array
     *
     * @param mixed $options
     *
     * @return void
     */
    public static function clickarea($options)
    {
        static::jquery();

        $arrayOptions = array();
        if (is_string($options)) {
            $arrayOptions = json_decode($options, true);
            if (!$arrayOptions) {
                $arrayOptions = array(
                    'selector' => $options
                );
            }
        } elseif (is_array($options)) {
            $arrayOptions = $options;
        }

        $jsonOptions = json_encode($arrayOptions);
        static::onready("$.Simplerenew.clickArea({$jsonOptions});");
    }

    /**
     * Add a script to run when dom ready
     *
     * @param string $js
     *
     * @return void
     */
    public static function onready($js)
    {
        $js = "(function($) { $(document).ready(function () { " . $js . " });})(jQuery);";
        SimplerenewFactory::getDocument()->addScriptDeclaration($js);
    }

    /**
     * Create an input form field
     *
     * @param string $name
     * @param mixed  $attribs
     * @param string $selected
     * @param mixed  $idtag
     *
     * @return string
     */
    public static function inputfield($name, $attribs = null, $selected = null, $idtag = false)
    {

        if ($attribs && !is_array($attribs)) {
            $attribs = JUtility::parseAttributes($attribs);
        }

        $attribs['name']  = $name;
        $attribs['id']    = $idtag ?: preg_replace('/(\[\]|\[|\])/', '_', $name);
        $attribs['type']  = 'text';
        $attribs['value'] = $selected;
        return '<input ' . SimplerenewUtilitiesArray::toString($attribs) . '/>';
    }

    /**
     * Turn any clickable element into an ajax submitter. See
     * media/js/utilities.js:ajax() for notes on defined tasks
     *
     * @param string $selector
     * @param array  $options
     *
     * @return void
     */
    public static function ajax($selector, $options = array())
    {
        static::jquery();

        $options = is_string($options) ? json_decode($options, true) : (array)$options;
        $options = array(
            'selector' => $selector,
            'ajax'     => $options
        );
        $options = json_encode($options);

        static::onready("$.Simplerenew.ajax({$options});");
    }
}
