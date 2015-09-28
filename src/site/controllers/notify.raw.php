<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;
use Simplerenew\Notify\Notify;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        JLog::addLogger(array('text_file' => 'simplerenew.log.php'), JLog::ALL, array('simplerenew'));
        $this->timeLog('BEGIN WEBHOOK', true);

        $user = $this->authenticate();
        $this->timeLog('Authenticate');

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

        // Send request to designated responder
        $containers = SimplerenewFactory::getAllGatewayContainers();
        $this->timeLog('Load Containers');

        $gateway = $app->input->getCmd('gateway');
        if (!isset($containers[$gateway])) {
            throw new NotFound(JText::sprintf('COM_SIMPLERENEW_ERROR_NOTIFY_INVALID_GATEWAY', $gateway));
        }

        /** @var Notify $notify */
        $notify = $containers[$gateway]->notify;
        $notify->process($package, $containers);
        $this->timeLog('Process package');

        if ($user) {
            $user->logout();
        }

        $this->timeLog('END WEBHOOK', true);
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

    /**
     * Log elapsed processing time
     *
     * @param string $message
     * @param bool   $divider
     */
    protected function timeLog($message, $divider = false)
    {
        static $start = null;
        static $lastEntry, $lastDivider;
        if ($start === null) {
            $start     = microtime(true);
            $lastEntry = $start;
        }

        $now       = microtime(true);
        $elapsed   = $now - $lastEntry;
        $lastEntry = $now;

        if ($divider) {
            if ($lastDivider !== null) {
                $message .= ' (' . number_format($now - $lastDivider, 4) . ')';
            }
            $message     = str_pad(' ' . $message . ' ', 40, '*', STR_PAD_BOTH);
            $lastDivider = $now;

        } else {
            $message = number_format($elapsed, 4) . ' ' . $message;
        }
        JLog::add($message, JLog::INFO);
    }
}
