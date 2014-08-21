<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerValidate extends SimplerenewControllerJson
{
    public function username()
    {
        $this->checkToken();

        if ($username = SimplerenewFactory::getApplication()->input->getUsername('username')) {
            $db = SimplerenewFactory::getDbo();

            $db->setQuery('Select id From #__users Where username=' . $db->quote($username));
            $id = $db->loadColumn();
        }

        echo json_encode(empty($id));
    }

    public function email()
    {
        $this->checkToken();

        if ($email = SimplerenewFactory::getApplication()->input->getString('email')) {
            $db = SimplerenewFactory::getDbo();

            $db->setQuery('Select id From #__users Where email=' . $db->quote($email));
            $id = $db->loadColumn();
        }

        echo json_encode(empty($id));
    }
}
