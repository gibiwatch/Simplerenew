<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        $this->authenticate();

        $path = JPATH_SITE . '/logs/simplerenew.txt';
        file_put_contents($path, date('Y-m-d H:i:s') . "\n", FILE_APPEND);

/*
        $post_xml = file_get_contents("php://input");
        $notification = new Recurly_PushNotification($post_xml);

        // Recurly notifications
        switch ($notification->type) {
            case 'new_account_notification':
            case 'canceled_account_notification':
            case 'billing_info_updated_notification':
            case 'reactivated_account_notification':

            case 'new_invoice_notification':
            case 'closed_invoice_notification':
            case 'past_due_invoice_notification':

            case 'new_subscription_notification':
            case 'updated_subscription_notification':
            case 'canceled_subscription_notification':
            case 'expired_subscription_notification':
            case 'renewed_subscription_notification':

            case 'successful_payment_notification':
            case 'failed_payment_notification':
            case 'successful_refund_notification':
            case 'void_payment_notification':
        }
*/
    }

    /**
     * Check credentials of caller
     *
     * @throws Exception
     */
    protected function authenticate()
    {
        $app = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $username = $app->input->server->getUsername('PHP_AUTH_USER');
        $password = $app->input->server->getString('PHP_AUTH_PW');

        if (!$username) {
            throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_USERNAME_REQUIRED'), 401);
        }

        // Check the password
        try {
            $user = $container->getUser()->loadByUsername($username);
        } catch (Simplerenew\Exception\NotFound $e) {
            throw new Exception($e->getMessage(), 401);
        }
        $user->validate($password);

        // Check for proper access
        $jUser = SimplerenewFactory::getUser($user->id);
        if (!$jUser->authorise('core.manage', 'com_simplerenew')) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 401);
        }
    }
}
