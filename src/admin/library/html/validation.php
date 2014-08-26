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
     * Add validation scripts for the subscribe screen
     *
     * @return void
     */
    public static function subscribe()
    {
        JHtml::_('sr.validation');

        $js = array(
            "jQuery(document).ready(function() {",
            "   jQuery.Simplerenew.validate.subscribe('#subscribeForm');",
            "});"
        );
        SimplerenewFactory::getDocument()
            ->addScriptDeclaration(join("\n", $js));

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
