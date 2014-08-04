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

    protected function authenticate()
    {
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $this->gatewayLogin(
            $app->input->server->getUsername('PHP_AUTH_USER'),
            $app->input->server->getString('PHP_AUTH_PW')
        );

    }

    /**
     * Check login credentials of caller if provided
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     * @throws Exception
     */
    protected function gatewayLogin($username, $password)
    {
        if ($username && $password) {
            $container = SimplerenewFactory::getContainer();

            // Login
            try {
                $user = $container->getUser()
                    ->loadByUsername($username);

                // Check for proper access
                $jUser = SimplerenewFactory::getUser($user->id);
                if ($jUser->authorise('core.manage', 'com_simplerenew')) {
                    $user->login($password);
                    return;
                }

            } catch (Exception $e) {
                throw new Exception($e->getMessage(), 403);
            }
        }
    }
}
