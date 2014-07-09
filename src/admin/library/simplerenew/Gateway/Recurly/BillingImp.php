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
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'region'    => 'state',
        'postal'    => 'zip',
        'ipaddress' => 'ip_address'
    );

    /**
     * @var array Associative array of \Recurly_BillingInfo objects already loaded
     */
    protected $accountsLoaded = array();

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

            if ($parent->address instanceof Address) {
                $parent->address->setProperties(
                    $billing,
                    array(
                        'region' => 'state',
                        'postal' => 'zip'
                    )
                );
            }

            $ppDev = \SimplerenewFactory::getApplication()->input->getInt('ppdev', 0);
            if ($ppDev) {
                $payment = new PayPal(array('agreementId' => '12345-TEST-54321'));

            } elseif ($billing->first_six && $billing->last_four) {
                $payment = new CreditCard(
                    array(
                        'month'     => $billing->month,
                        'year'      => $billing->year,
                        'type'      => $billing->card_type,
                        'lastFour' => $billing->last_four
                    )
                );
            } elseif ($billing->paypal_billing_agreement_id) {
                $payment              = new PayPal();
                $payment->agreementId = $billing->paypal_billing_agreement_id;
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
        $billing = $this->getBilling($parent->account->code);

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

        if ($parent->payment instanceof CreditCard) {
            /** @var CreditCard $cc */
            $cc              = $parent->payment;
            $billing->number = $cc->number;
            $billing->month  = $cc->month;
            $billing->year   = $cc->year;
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
            if (empty($this->accountsLoaded[$accountCode])) {
                $this->accountsLoaded[$accountCode] = \Recurly_BillingInfo::get($accountCode, $this->client);
            }

        } catch (\Recurly_NotFoundError $e) {
            // Need to have blank/default billing for an existing account
            $newBilling                         = new \Recurly_BillingInfo(null, $this->client);
            $newBilling->account_code           = $accountCode;
            $this->accountsLoaded[$accountCode] = $newBilling;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $this->accountsLoaded[$accountCode];
    }
}
