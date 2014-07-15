<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends SimplerenewControllerBase
{
    protected $default_view = 'plans';

    public function test()
    {
        $user = SimplerenewFactory::getUser();
        $groupId = 7;

        echo '<pre>';
        var_dump(JAccess::getAssetRules(1)->allow('core.admin', $groupId));
        var_dump(JAccess::checkGroup($groupId, 'core.admin'));
        echo '</pre>';
        //if (JAccess::getAssetRules(1)->allow('core.admin', $identities))

    }
}
