<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Transaction;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\TransactionInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class TransactionImp extends AbstractRecurlyBase implements TransactionInterface
{
    protected $fieldMap = array(
        'id'      => 'uuid',
        'status'  => array(
            'status' => array(
                'success'             => Transaction::STATUS_SUCCESS,
                'failed'              => Transaction::STATUS_FAILED,
                'void'                => Transaction::STATUS_VOID,
                Object::MAP_UNDEFINED => Transaction::STATUS_UNKNOWN
            )
        ),
        'action'  => array(
            'action' => array(
                'purchase'            => Transaction::ACTION_PURCHASE,
                'authorization'       => Transaction::ACTION_AUTH,
                'refund'              => Transaction::ACTION_REFUND,
                Object::MAP_UNDEFINED => Transaction::ACTION_UNKNOWN
            )
        ),
        'method'  => array(
            'payment_method' => array(
                'credit_card'         => Transaction::METHOD_CARD,
                'paypal'              => Transaction::METHOD_PAYPAL,
                'check'               => Transaction::METHOD_CHECK,
                'wire_transfer'       => Transaction::METHOD_WIRE,
                'money_order'         => Transaction::METHOD_MONEY_ORDER,
                Object::MAP_UNDEFINED => Transaction::METHOD_UNKNOWN
            )
        ),
        'created' => 'created_at'
    );

    /**
     * Retrieve a specific Transaction
     *
     * @param Transaction $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Transaction $parent)
    {
        $transaction = $this->getTransaction($parent->id);
        $this->bindSource($parent, $transaction);
    }

    /**
     * Get list of transactions for account
     *
     * @param Transaction $template
     * @param Account     $account
     * @param int         $statusMask Bitmask of Transaction statuses to return
     *
     * @return array
     */
    public function getList(Transaction $template, Account $account, $statusMask = null)
    {
        /**
         * @var \Recurly_Transaction $rawTransaction
         */
        $transactions = array();

        $list = \Recurly_TransactionList::getForAccount($account->code, null, $this->client);
        foreach ($list as $rawTransaction) {
            $status = $this->translateStatus($rawTransaction->status);

            if (!$statusMask || ($statusMask & $status)) {
                $transaction = clone $template;
                $this->bindSource($transaction, $rawTransaction);

                $transactions[$transaction->id] = $transaction;
            }
        }
        return $transactions;
    }

    /**
     * @param string $id
     *
     * @return \Recurly_Transaction
     * @throws Exception
     */
    protected function getTransaction($id)
    {
        if (!$id) {
            throw new Exception('No Transaction ID given');
        }

        try {
            $transaction = \Recurly_Transaction::get($id, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $transaction;
    }

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Transaction $parent
     * @param mixed       $data
     *
     * @return void
     */
    public function bindSource(Transaction $parent, $data)
    {
        $converted = $parent->getProperties();
        $converted = $this->map($data, array_keys($converted), $this->fieldMap);

        if ($data instanceof \Recurly_Transaction) {
            \Recurly_Client::$apiKey = $this->client->apiKey();

            $converted = array_merge(
                $converted,
                array(
                    'amount'         => $this->getKeyValue($data, 'amount_in_cents') / 100,
                    'tax'            => $this->getKeyValue($data, 'tax_in_cents') / 100,
                    'accountCode'    => $data->account ? $data->account->get()->account_code : null,
                    'invoiceNumber'  => $data->invoice ? $data->invoice->get()->invoice_number : null,
                    'subscriptionId' => $data->subscription ? $data->subscription->get()->uuid : null
                )
            );
        }
        $converted['created'] = $this->toDateTime($converted['created']);

        $parent->setProperties($converted);
    }

    /**
     * Translate a Recurly Status to an API Status
     *
     * @param string $status
     *
     * @return string
     */
    protected function translateStatus($status)
    {
        if (isset($this->fieldMap['status']['status'][$status])) {
            return $this->fieldMap['status']['status'][$status];
        }
        return null;
    }
}
