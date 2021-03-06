<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\AutoLoader;
use Simplerenew\Cms\Joomla\Plugin;

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgSimplerenewRecurly extends Plugin
{
    public function __construct(&$subject, $config = array())
    {
        AutoLoader::register('Simplerenew', __DIR__ . '/library/simplerenew');

        parent::__construct($subject, $config);
    }

    public function onContentPrepareForm(JForm $form, $data)
    {
        $app = JFactory::getApplication();

        if ($app->isAdmin()) {
            $name = $form->getName() . '.' . $app->input->getCmd('component');
            if ($name == 'com_config.component.com_simplerenew') {
                $path = __DIR__ . '/recurly.xml';
                $form->loadFile($path, false, '//simplerenew/config');
            }
        }
    }

    /**
     * Additional Recurly JS for using billing tokens to validate sensitive
     * data like credit card numbers.
     *
     * @return array
     */
    public function simplerenewAdditionalValidation()
    {
        $js = array(
            'https://js.recurly.com/v3/recurly.js',
            ':com_simplerenew/recurly/validation.js'
        );

        $config = SimplerenewFactory::getContainer()->configuration;

        $mode = $config->get('gateway.mode');
        $key  = $config->get("gateway.{$mode}.publicKey");

        $js[] = "(function($) {"
            . "$.Simplerenew.validate.gateway.options"
            . " = $.extend({},"
            . " $.Simplerenew.validate.gateway.options,"
            . " {key: '{$key}'});"
            . "})(jQuery);";

        return $js;
    }

    /**
     * Provide namespace for our gateway classes
     *
     * @return string
     */
    public function simplerenewLoadGateway()
    {
        return 'Recurly';
    }
}
