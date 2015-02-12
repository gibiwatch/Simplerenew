<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends SimplerenewControllerBase
{
    protected $default_view = 'plans';

    public function test()
    {
        $addon = SimplerenewHelper::getExtensionTable('com_srgroupleaders');

        SimplerenewAddon::register('Group Leaders', $addon);
    }
}
