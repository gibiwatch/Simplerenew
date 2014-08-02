<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        $app = SimplerenewFactory::getApplication();

        $method = $app->input->getMethod();
        switch ($method) {
            case 'POST':
                $this->authenticate();
                $package = file_get_contents('php://input');
                break;

            default:
                throw new Exception('not accepting ' . $method);
                break;
        }

        $container = SimplerenewFactory::getContainer();
        $notify    = $container->getNotify();
        $notify->process($package, $container);
    }

    /**
     * Check credentials of caller
     *
     * @throws Exception
     */
    protected function authenticate()
    {
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $username = $app->input->server->getUsername('PHP_AUTH_USER');
        $password = $app->input->server->getString('PHP_AUTH_PW');

        if ($username) {
            // Check the password
            try {
                $user = $container->getUser()->loadByUsername($username);
            } catch (NotFound $e) {
                throw new Exception($e->getMessage(), 403);
            }

            if ($user->validate($password)) {
                // Check for proper access
                $jUser = SimplerenewFactory::getUser($user->id);
                if ($jUser->authorise('core.manage', 'com_simplerenew')) {
                    return;
                }
            }
        }

        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
    }
}
