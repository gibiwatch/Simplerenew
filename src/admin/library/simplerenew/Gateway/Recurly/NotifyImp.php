<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Subscription;
use Simplerenew\Exception;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Notify\Notify;
use Simplerenew\Object;

defined('_JEXEC') or die();

class NotifyImp extends AbstractRecurlyBase implements NotifyInterface
{
    protected $validIpAddresses = array(
        '75.98.92.96/28',  // < 2014-09-10
        '74.201.212.0/24',
        '64.74.141.0/24'
    );

    protected $fieldMap = array(
        'type'   => array(
            'type' => array(
                'new_account_notification'           => Notify::TYPE_ACCOUNT,
                'canceled_account_notification'      => Notify::TYPE_ACCOUNT,
                'reactivated_account_notification'   => Notify::TYPE_ACCOUNT,
                'billing_info_updated_notification'  => Notify::TYPE_BILLING,
                'new_invoice_notification'           => Notify::TYPE_INVOICE,
                'closed_invoice_notification'        => Notify::TYPE_INVOICE,
                'past_due_invoice_notification'      => Notify::TYPE_INVOICE,
                'new_subscription_notification'      => Notify::TYPE_SUBSCRIPTION,
                'updated_subscription_notification'  => Notify::TYPE_SUBSCRIPTION,
                'canceled_subscription_notification' => Notify::TYPE_SUBSCRIPTION,
                'expired_subscription_notification'  => Notify::TYPE_SUBSCRIPTION,
                'renewed_subscription_notification'  => Notify::TYPE_SUBSCRIPTION,
                'successful_payment_notification'    => Notify::TYPE_PAYMENT,
                'failed_payment_notification'        => Notify::TYPE_PAYMENT,
                'successful_refund_notification'     => Notify::TYPE_PAYMENT,
                'void_payment_notification'          => Notify::TYPE_PAYMENT,
                Object::MAP_UNDEFINED                => Notify::TYPE_UNKNOWN
            )
        ),
        'action' => array(
            'type' => array(
                'new_account_notification'           => Notify::ACTION_NEW,
                'canceled_account_notification'      => Notify::ACTION_CANCEL,
                'billing_info_updated_notification'  => Notify::ACTION_UPDATE,
                'reactivated_account_notification'   => Notify::ACTION_REACTIVATE,
                'new_invoice_notification'           => Notify::ACTION_NEW,
                'closed_invoice_notification'        => Notify::ACTION_CLOSE,
                'past_due_invoice_notification'      => Notify::ACTION_PAST_DUE,
                'new_subscription_notification'      => Notify::ACTION_NEW,
                'updated_subscription_notification'  => Notify::ACTION_UPDATE,
                'canceled_subscription_notification' => Notify::ACTION_CANCEL,
                'expired_subscription_notification'  => Notify::ACTION_EXPIRE,
                'renewed_subscription_notification'  => Notify::ACTION_RENEW,
                'successful_payment_notification'    => Notify::ACTION_SUCCESS,
                'failed_payment_notification'        => Notify::ACTION_FAIL,
                'successful_refund_notification'     => Notify::ACTION_REFUND,
                'void_payment_notification'          => Notify::ACTION_VOID,
                Object::MAP_UNDEFINED                => Notify::ACTION_UNKNOWN
            )
        ),
        'user'   => null
    );

    /**
     * Translate a notification message from the gateway into
     * a Notify object. All validation of the message and its
     * source should be done here.
     *
     * @param Notify $parent
     * @param mixed  $package
     *
     * @return void
     * @throws Exception
     */
    public function loadPackage(Notify $parent, $package)
    {
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        if (!$parent->IPAllowed($ip, $this->validIpAddresses)) {
            throw new Exception('Notice came from unrecognized IP - ' . $ip);
        }

        $xml = simplexml_load_string($package);

        $data = array(
            'type'    => $xml->getName(),
            'package' => $package
        );

        foreach ($xml->children() as $node) {
            $name = $node->getName();
            if ($node->count()) {
                $value = $this->xmlNodeToObject($node);
            } else {
                $value = (string)$node;
            }
            $data[$name] = $value;
        }

        // Adjust Webhook responses for standard API classes
        if (!empty($data['subscription'])) {
            $data['subscription']['plan_code'] = $data['subscription']['plan']['plan_code'];
            if (!empty($data['account'])) {
                $data['subscription']['account_code'] = $data['account']['account_code'];
            }
        }

        if (!empty($data['transaction'])) {
            if (!empty($data['account'])) {
                $data['transaction']['accountCode'] = $data['account']['account_code'];
            }
            $data['transaction'] = array_merge(
                $data['transaction'],
                array(
                    'uuid'           => $data['transaction']['id'],
                    'created_at'     => $data['transaction']['date'],
                    'amount'         => $data['transaction']['amount_in_cents'] / 100,
                    'invoiceNumber'  => $data['transaction']['invoice_number'],
                    'subscriptionId' => $data['transaction']['subscription_id']
                )
            );
        }

        $parent->setProperties($data, $this->fieldMap);
    }

    protected function xmlNodeToObject(\SimpleXMLElement $node)
    {
        $values = array();
        foreach ($node->children() as $child) {
            $name = $child->getName();
            if ($child->count()) {
                $value = $this->xmlNodeToObject($child);
            } else {
                $value = (string)$child;
            }
            $values[$name] = $value;
        }

        return $values;
    }
}
