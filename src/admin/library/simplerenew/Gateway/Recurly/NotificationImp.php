<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Notification;
use Simplerenew\Gateway\NotificationInterface;

defined('_JEXEC') or die();

class NotificationImp extends AbstractRecurlyBase implements NotificationInterface
{
    protected $fieldMap = array(
        'type'   => array(
            'type' => array(
                'new_account_notification'           => Notification::TYPE_ACCOUNT,
                'canceled_account_notification'      => Notification::TYPE_ACCOUNT,
                'billing_info_updated_notification'  => Notification::TYPE_ACCOUNT,
                'reactivated_account_notification'   => Notification::TYPE_ACCOUNT,
                'new_invoice_notification'           => Notification::TYPE_INVOICE,
                'closed_invoice_notification'        => Notification::TYPE_INVOICE,
                'past_due_invoice_notification'      => Notification::TYPE_INVOICE,
                'new_subscription_notification'      => Notification::TYPE_SUBSCRIPTION,
                'updated_subscription_notification'  => Notification::TYPE_SUBSCRIPTION,
                'canceled_subscription_notification' => Notification::TYPE_SUBSCRIPTION,
                'expired_subscription_notification'  => Notification::TYPE_SUBSCRIPTION,
                'renewed_subscription_notification'  => Notification::TYPE_SUBSCRIPTION,
                'successful_payment_notification'    => Notification::TYPE_PAYMENT,
                'failed_payment_notification'        => Notification::TYPE_PAYMENT,
                'successful_refund_notification'     => Notification::TYPE_PAYMENT,
                'void_payment_notification'          => Notification::TYPE_PAYMENT
            )
        ),
        'action' => array(
            'type' => array(
                'new_account_notification'           => Notification::ACTION_NEW,
                'canceled_account_notification'      => Notification::ACTION_CANCEL,
                'billing_info_updated_notification'  => Notification::ACTION_UPDATE,
                'reactivated_account_notification'   => Notification::ACTION_REACTIVATE,
                'new_invoice_notification'           => Notification::ACTION_NEW,
                'closed_invoice_notification'        => Notification::ACTION_CLOSE,
                'past_due_invoice_notification'      => Notification::ACTION_PAST_DUE,
                'new_subscription_notification'      => Notification::ACTION_NEW,
                'updated_subscription_notification'  => Notification::ACTION_UPDATE,
                'canceled_subscription_notification' => Notification::ACTION_CANCEL,
                'expired_subscription_notification'  => Notification::ACTION_EXPIRE,
                'renewed_subscription_notification'  => Notification::ACTION_RENEW,
                'successful_payment_notification'    => Notification::ACTION_SUCCESS,
                'failed_payment_notification'        => Notification::ACTION_FAIL,
                'successful_refund_notification'     => Notification::ACTION_REFUND,
                'void_payment_notification'          => Notification::ACTION_VOID
            )
        )
    );


    public function loadPackage(Notification $parent, $package)
    {
        $notice = new \Recurly_PushNotification($package);
        $parent->setProperties($notice, $this->fieldMap);

        echo '<pre>';
        print_r($parent->getProperties());
        print_r($notice);

        echo '</pre>';
    }
}
