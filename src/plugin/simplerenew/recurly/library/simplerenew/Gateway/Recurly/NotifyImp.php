<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
        '76.105.255.197', // BT Testing
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
     * @param string $package
     *
     * @return void
     * @throws Exception
     */
    public function loadPackage(Notify $parent, $package)
    {
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        if (!$parent->IPAllowed($ip, $this->validIpAddresses)) {
            throw new NotAuthorised('Notice came from unrecognized IP - ' . $ip);
        }

        $xml = simplexml_load_string($package);

        $parent->setProperties(
            array(
                'type'    => $xml->getName(),
                'package' => $package
            ),
            $this->fieldMap
        );

        $data = array();
        foreach ($xml->children() as $node) {
            $name = $node->getName();
            if ($node->count()) {
                $value = $this->xmlNodeToObject($node);
            } else {
                $value = (string)$node;
            }
            $data[$name] = $value;
        }

        $container = $parent->getContainer();

        if (!empty($data['subscription']['uuid'])) {
            $subscriptionId = $data['subscription']['uuid'];
        } elseif (!empty($data['invoice']['subscription_id'])) {
            $subscriptionId = $data['invoice']['subscription_id'];
        }
        if (!empty($subscriptionId)) {
            $parent->subscription = $container->subscription->load($subscriptionId);
        }

        if (!empty($data['account']['account_code'])) {
            $parent->account = $container->account;
            try {
                $parent->account->loadByAccountCode($data['account']['account_code']);
                $parent->user = $parent->account->user;

            } catch (Exception $e) {
                // This should handle possibly deleted users
                $parent->account->bindSource($data['account']);
            }
            $parent->billing = $container->billing->load($parent->account);
        }

        if (!empty($data['invoice'])) {
            $parent->invoice = $container->invoice->bindSource($data['invoice']);
        }

        // Reformat transaction data for Transaction class
        if (!empty($data['transaction'])) {
            if ($parent->account->code) {
                $data['transaction']['account_code'] = $parent->account->code;
            }

            $data['transaction'] = array_merge(
                $data['transaction'],
                array(
                    'uuid'       => $data['transaction']['id'],
                    'created_at' => $data['transaction']['date'],
                    'amount'     => $data['transaction']['amount_in_cents'] / 100
                )
            );
            $parent->transaction = $container->transaction->bindSource($data['transaction']);
        }
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
