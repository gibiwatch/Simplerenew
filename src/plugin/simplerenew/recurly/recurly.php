<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
}
