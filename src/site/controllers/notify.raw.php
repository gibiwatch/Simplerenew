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

        switch ($notification->type) {
            case 'new_account_notification':
                break;

            case 'canceled_account_notification':
                $accountCode = (string)$notification->account->account_code;
                $account     = new RecurlyApiAccount($accountCode);

                RecurlyHelper::updateUser($account->userId);
                RecurlyHelper::closeUser($account->userId);
                break;

            case 'billing_info_updated_notification':
                break;

            case 'reactivated_account_notification':
                break;

            case 'new_subscription_notification':
                // Fall through
            case 'updated_subscription_notification':
                $accountCode = (string)$notification->account->account_code;
                $planCode    = (string)$notification->subscription->plan->plan_code;

                $account = new RecurlyApiAccount($accountCode);
                RecurlyHelper::updateUser($account->userId, $planCode);
                break;

            case 'canceled_subscription_notification':
                break;

            case 'renewed_subscription_notification':
                list ($userId) = preg_split('/_/', $notification->account->account_code);

                $planCode = $notification->subscription->plan->plan_code . '';
                RecurlyHelper::updateUser($userId, $planCode);
                break;

            case 'successful_payment_notification':
                RecurlyHelper::successInvoice($notification);
                break;

            case 'failed_payment_notification':
                // Change to Registered if payment is failed
                RecurlyHelper::failedInvoice($notification);
                $subscription = null;
                try {
                    $subscriptions = Recurly_SubscriptionList::getForAccount($notification->account->account_code);

                    foreach ($subscriptions as $item) {
                        $subscription = $item;
                        break;
                    }
                    if ($subscription && $subscription->state == 'active') {
                        break;
                    }
                } catch (Exception $e) {
                }
            // Fall through

            case 'expired_subscription_notification':
                list (, $userId) = preg_split("/_/", $notification->account->account_code);
                RecurlyHelper::updateUser($userId);
                break;

            case 'successful_refund_notification':
                break;

            case 'void_payment_notification':
                break;
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
