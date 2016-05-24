<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Exception;
use Simplerenew\Exception\NotAuthorised;
use Simplerenew\Gateway\NotifyInterface;
use Simplerenew\Notify\Notify;
use Simplerenew\Object;

defined('_JEXEC') or die();

class NotifyImp extends AbstractRecurlyBase implements NotifyInterface
{
    protected $validIpAddresses = array(
        '50.0.172.150',
        '50.18.192.88',
        '52.203.102.94',
        '52.203.192.184',
        '52.8.32.100',
        '52.9.209.233'
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
     * @param string $package
     *
     * @return array
     * @throws Exception
     */
    public function loadPackage($package)
    {
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        if (!$this->IPAllowed($ip, $this->validIpAddresses)) {
            throw new NotAuthorised('Notice came from unrecognized IP - ' . $ip);
        }

        $xml = simplexml_load_string($package);

        $data = array(
            'type'    => $xml->getName(),
            'action'  => $xml->getName(),
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

        // Reformat subscription data for Subscription class
        if (!empty($data['subscription'])) {
            if (empty($data['plan'])) {
                $data['plan'] = $data['subscription']['plan'];
            }

            $data['subscription']['plan_code'] = $data['subscription']['plan']['plan_code'];
            if (!empty($data['account'])) {
                $data['subscription']['account_code'] = $data['account']['account_code'];
            }
        }

        // Create a stub Subscription class from invoice if not already included
        if (!empty($data['invoice']) && empty($data['subscription'])) {
            $data['subscription']['uuid'] = $data['invoice']['subscription_id'];
        }

        // Reformat transaction data for Transaction class
        if (!empty($data['transaction'])) {
            // Create a stub Subscription class if not already included
            if (empty($data['subscription'])) {
                $data['subscription']['uuid'] = $data['transaction']['subscription_id'];
            }

            if (!empty($data['account'])) {
                $data['transaction']['account_code'] = $data['account']['account_code'];
            }

            $data['transaction'] = array_merge(
                $data['transaction'],
                array(
                    'uuid'       => $data['transaction']['id'],
                    'created_at' => $data['transaction']['date'],
                    'amount'     => $data['transaction']['amount_in_cents'] / 100
                )
            );
        }


        return $this->map($data, array_keys($data), $this->fieldMap);
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
