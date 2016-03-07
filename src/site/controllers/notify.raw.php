<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\AbstractLogger as Logger;
use Simplerenew\Exception\NotFound;
use Simplerenew\Notify\Notify;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        // Back out all buffering so gateway knows we're here
        while (ob_get_status()) {
            ob_end_flush();
        }

        $logger = SimplerenewFactory::getContainer()->logger;
        $logger->debug('Notification: Begin Receive', Logger::DEBUG_INFO, true);

        $user = $this->authenticate();

        $app    = SimplerenewFactory::getApplication();
        $method = $app->input->getMethod();

        switch ($method) {
            case 'POST':
                $package = file_get_contents('php://input');
                break;

            default:
                $logger->debug("[receive] Error: bad method [{$method}]", Logger::DEBUG_ERROR);
                throw new Exception(JText::sprintf('COM_SIMPLERENEW_ERROR_NOTIFY_METHOD', $method), 405);
                break;
        }
        $logger->debug("Raw body: {$package}");

        // Send request to designated responder
        $containers = SimplerenewFactory::getAllGatewayContainers();
        $logger->debug('Available gateways: ' . join(',', array_keys($containers)));

        $gateway = $app->input->getCmd('gateway');
        if (!isset($containers[$gateway])) {
            $logger->debug("[receive] Error: gateway not found [{$gateway}]", Logger::DEBUG_ERROR);
            throw new NotFound(JText::sprintf('COM_SIMPLERENEW_ERROR_NOTIFY_INVALID_GATEWAY', $gateway));
        }

        /** @var Notify $notify */
        $notify = $containers[$gateway]->notify;
        $notify->process($package, $containers);

        if ($user) {
            $user->logout();
        }

        $logger->debug("Processed Successfully by {$gateway} plugin", Logger::DEBUG_INFO, true);

        jexit();
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
