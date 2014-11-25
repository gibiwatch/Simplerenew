<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Invoice;
use Simplerenew\Exception;
use Simplerenew\Gateway\InvoiceInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class InvoiceImp extends AbstractRecurlyBase implements InvoiceInterface
{
    protected $fieldMap = array(
        'id'          => 'uuid',
        'status'      => array(
            'state' => array(
                'open'                => Invoice::STATUS_OPEN,
                'collected'           => Invoice::STATUS_PAID,
                'failed'              => Invoice::STATUS_OPEN,
                'past_due'            => Invoice::STATUS_PAST_DUE,
                Object::MAP_UNDEFINED => Invoice::STATUS_UNKNOWN
            )
        ),
        'number'      => 'invoice_number',
        'date' => 'created_at'
    );


    /**
     * @param Invoice $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Invoice $parent)
    {
        // TODO: Implement load() method.
    }

    /**
     * @param Invoice $template
     * @param Account $account
     *
     * @return array
     * @throws Exception
     */
    public function getAccountList(Invoice $template, Account $account)
    {
        $invoices = array();

        $list = \Recurly_InvoiceList::getForAccount($account->code, null, $this->client);
        foreach ($list as $rawInvoice) {
            $invoice = clone $template;
            $this->bindSource($invoice, $rawInvoice);

            $invoices[$invoice->id] = $invoice;
        }

        return $invoices;
    }

    public function bindSource(Invoice $parent, $data)
    {
        // Find account code
        $account = $this->getKeyValue($data, 'account');

        if ($account instanceof \Recurly_Stub) {
            \Recurly_Client::$apiKey = $this->client->apiKey();
            $rawAccount              = $data->account->get();
            $accountCode             = $this->getKeyValue($rawAccount, 'account_code');
        } else {
            $accountCode = $this->getKeyValue($account, 'account_code');
        }

        // Find Subscription ID
        $subscription = $this->getKeyValue($data, 'subscription');

        if ($subscription instanceof \Recurly_Stub) {
            \Recurly_Client::$apiKey = $this->client->apiKey();
            $rawSubscription         = $data->subscription->get();
            $subscriptionId          = $this->getKeyValue($rawSubscription, 'uuid');
        } else {
            $subscriptionId = $this->getKeyValue($data, 'subscription');
        }

        $parent
            ->clearProperties()
            ->setProperties($data, $this->fieldMap)
            ->setProperties(
                array(
                    'account_code'    => $accountCode,
                    'subscription_id' => $subscriptionId,
                    'subtotal'        => $this->getKeyValue($data, 'subtotal_in_cents') / 100,
                    'tax'             => $this->getKeyValue($data, 'tax_in_cents') / 100,
                    'total'           => $this->getKeyValue($data, 'total_in_cents') / 100
                )
            );
    }
}
