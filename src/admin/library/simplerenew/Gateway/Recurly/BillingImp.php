<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Billing;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Primitive\Address;
use Simplerenew\Primitive\CreditCard;
use Simplerenew\Primitive\PayPal;

defined('_JEXEC') or die();

class BillingImp extends AbstractRecurlyBase implements BillingInterface
{
    protected $fieldMap = array(
        'firstname'   => 'first_name',
        'lastname'    => 'last_name',
        'region'      => 'state',
        'postal'      => 'zip',
        'ipaddress'   => 'ip_address',
        'type'        => 'card_type',
        'lastFour'    => 'last_four',
        'agreementId' => 'paypal_billing_agreement_id'
    );

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Billing $parent)
    {
        $parent->clearProperties();

        $billing = $this->getBilling($parent->account->code);
        if ($billing) {
            $parent->setProperties($billing, $this->fieldMap);

            // Recognize debugging url var
            if (\SimplerenewFactory::getApplication()->input->getInt('ppdev', 0)) {
                $billing->paypal_billing_agreement_id = '12345-TEST-54321';
            }

            if ($billing->paypal_billing_agreement_id) {
                $payment = new PayPal();
                $payment->setProperties($billing, $this->fieldMap);

            } elseif ($billing->first_six && $billing->last_four) {
                $payment = new CreditCard();
                $payment->setProperties($billing, $this->fieldMap);

            } else {
                $payment = null;
            }

            $parent->setPayment($payment);
        }
    }

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function save(Billing $parent)
    {
        $accountCode = $parent->account->code;

        /** @var CreditCard $cc */
        if ($parent->payment instanceof CreditCard) {
            $cc = $parent->payment;
        }

        try {
            $billing = $this->getBilling($accountCode);

        } catch (NotFound $e) {
            // Let's see if we have what it takes to create
            if (empty($cc) || empty($cc->number)) {
                return;
            }

            $billing               = new \Recurly_BillingInfo(null, $this->client);
            $billing->account_code = $accountCode;
        }

        $billing->first_name = $parent->firstname;
        $billing->last_name  = $parent->lastname;
        $billing->phone      = $parent->phone;
        $billing->ip_address = $parent->ipaddress;

        $billing->address1 = $parent->address->address1;
        $billing->address2 = $parent->address->address2;
        $billing->city     = $parent->address->city;
        $billing->state    = $parent->address->region;
        $billing->country  = $parent->address->country;
        $billing->zip      = $parent->address->postal;

        if ($cc) {
            $billing->number             = $cc->number;
            $billing->verification_value = $cc->cvv;
            $billing->month              = $cc->month;
            $billing->year               = $cc->year;
        }

        try {
            $billing->update();

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Billing $parent)
    {
        $accountCode = $parent->account->code;

        try {
            $billing = $this->getBilling($accountCode);

            try {
                $billing->delete();
            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        } catch (NotFound $e) {
            // Perfectly fine - no billing info to delete
        }

        if (isset($this->accountsLoaded[$accountCode])) {
            unset($this->accountsLoaded[$accountCode]);
        }
    }

    /**
     * @param $accountCode
     *
     * @return \Recurly_BillingInfo
     * @throws Exception
     */
    protected function getBilling($accountCode)
    {
        try {
            $billing = \Recurly_BillingInfo::get($accountCode, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $billing;
    }
}
