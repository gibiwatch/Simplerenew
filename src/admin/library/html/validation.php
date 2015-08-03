<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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

        JHtml::_('sr.onready', "$.Simplerenew.validate.init('{$selector}');");

        // additional validation code - to be expected from the gateways
        $additionalJS = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(
                SimplerenewFactory::getContainer()
                    ->events
                    ->trigger('simplerenewAdditionalValidation')
            )
        );

        foreach ($additionalJS as $js) {
            if (substr($js, 0, 4) == 'http') {
                JHtml::_('script', $js);
            } elseif ($js[0] == ':') {
                jhtml::_('script', substr($js, 1), false, true);
            } else {
                SimplerenewFactory::getDocument()
                    ->addScriptDeclaration($js);
            }
        }
    }
}
