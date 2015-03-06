<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        $user = $this->authenticate();

        $app    = SimplerenewFactory::getApplication();
        $method = $app->input->getMethod();

        switch ($method) {
            case 'POST':
                $package = file_get_contents('php://input');
                break;

            default:
                throw new Exception(JText::sprintf('COM_SIMPLERENEW_ERROR_NOTIFY_METHOD', $method), 405);
                break;
        }

        $container = SimplerenewFactory::getContainer();
        $notify    = $container->getNotify();
        $notify->process($package, $container);

        if ($user) {
            $user->logout();
        }
    }

    /**
     * First pass authentication. Two possibilities are accepted.
     * If HTTP Authentication is turned on for this page and has
     * not been accepted, we will look for a Joomla user with
     * the passed username and authenticate against that password.
     * The Joomla user must have at least core.manage permission.
     *
     * @return User|null
     * @throws Exception
     */
    protected function authenticate()
    {
        $app = SimplerenewFactory::getApplication();

        $authType = $app->input->server->get('AUTH_TYPE');
        if (!$authType) {
            $username = $app->input->server->getUsername('PHP_AUTH_USER');
            $password = $app->input->server->getString('PHP_AUTH_PW');

            if ($username && $password) {
                // U/P given but not authenticated by HTTP Auth
                $container = SimplerenewFactory::getContainer();

                try {
                    $user = $container->getUser()
                        ->loadByUsername($username);

                    // Check for proper access
                    $jUser = SimplerenewFactory::getUser($user->id);
                    if ($jUser->authorise('core.manage', 'com_simplerenew')) {
                        $user->login($password);
                        return $user;
                    }

                } catch (Exception $e) {
                    throw new Exception($e->getMessage(), 403);
                }
            }
        }

        return null;
    }
}
