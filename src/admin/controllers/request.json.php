<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerRequest extends SimplerenewControllerJson
{
    public function display($cachable = false, $urlparams = false)
    {
        throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_INVALID_REQUEST'), 404);
    }

    public function user()
    {
        $app = SimplerenewFactory::getApplication();

        $userId = $app->input->getInt('id');

        $user = SimplerenewFactory::getUser($userId)->getProperties();
        unset($user['password']);

        echo json_encode($user);
    }
}
