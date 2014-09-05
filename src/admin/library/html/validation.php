<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SrValidation
{
    /**
     * Load js support for jQuery form validation
     *
     * @param string $selector
     *
     * @return void;
     */
    public static function init($selector)
    {
        JHtml::_('sr.jquery');

        JHtml::_('script', 'com_simplerenew/validation/jquery.validate.js', false, true);
        JHtml::_('script', 'com_simplerenew/validation.js', false, true);
        JHtml::_('script', 'com_simplerenew/creditcard.js', false, true);

        JHtml::_('sr.onready', "jQuery.Simplerenew.validate.init('{$selector}');");
    }

    /**
     * Add gateway specific js support for payment screens
     *
     * @return void
     */
    public static function billing()
    {
        $jsAssets = SimplerenewFactory::getContainer()
            ->getBilling()
            ->getJSAssets();

        foreach ($jsAssets as $js) {
            if (substr($js, 0, 4) == 'http') {
                JHtml::_('script', $js);
            } elseif ($js[0] == '/') {
                jhtml::_('script', 'com_simplerenew/gateway' . $js, false, true);
            } else {
                SimplerenewFactory::getDocument()
                    ->addScriptDeclaration($js);
            }
        }
    }
}
