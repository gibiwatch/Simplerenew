<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Invoice;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\InvoiceInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class InvoiceImp extends AbstractRecurlyBase implements InvoiceInterface
{
    protected $fieldMap = array(
        'id'     => 'uuid',
        'number' => 'invoice_number',
        'status' => array(
            'state' => array(
                'open'                => Invoice::STATUS_OPEN,
                'collected'           => Invoice::STATUS_PAID,
                'failed'              => Invoice::STATUS_OPEN,
                'past_due'            => Invoice::STATUS_PAST_DUE,
                Object::MAP_UNDEFINED => Invoice::STATUS_UNKNOWN
            )
        ),
        'date'   => 'created_at'
    );

    /**
     * @param Invoice $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Invoice $parent)
    {
        $invoice = $this->getInvoice($parent->number);
        $this->bindSource($parent, $invoice);
    }

    /**
     * Return all invoices for the selected account
     *
     * @param Invoice $template
     * @param Account $account
     *
     * @return array
     * @throws Exception
     */
    public function getAccountList(Invoice $template, Account $account)
    {
        $invoices = array();

        try {
            $list = \Recurly_InvoiceList::getForAccount($account->code, null, $this->client);
            foreach ($list as $rawInvoice) {
                $invoice = clone $template;
                $this->bindSource($invoice, $rawInvoice);

                $invoices[$invoice->number] = $invoice;
            }

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $invoices;
    }

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Invoice $parent
     * @param mixed   $data
     *
     * @throws Exception
     */
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

    /**
     * @param string $number
     *
     * @return \Recurly_Invoice
     * @throws Exception
     * @throws NotFound
     */
    protected function getInvoice($number)
    {
        if (!$number) {
            throw new Exception('No invoice selected');
        }

        try {
            $invoice = \Recurly_Invoice::get($number, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $invoice;
    }

    /**
     * Return an invoice as pdf
     *
     * @param Invoice $parent
     *
     * @return string
     * @throws Exception
     */
    public function toPDF(Invoice $parent)
    {
        $invoice = $this->getInvoice($parent->number);
        return $invoice->getPdf();
    }
}
