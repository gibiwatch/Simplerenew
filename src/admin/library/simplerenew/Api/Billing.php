<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Primitive\AbstractPayment;
use Simplerenew\Primitive\Address;
use Simplerenew\Primitive\CreditCard;

defined('_JEXEC') or die();

/**
 * Class Billing
 * @package Simplerenew\Api
 *
 * @property-read Account         $account
 * @property-read Address         $address
 * @property-read AbstractPayment $payment
 */
class Billing extends AbstractApiBase
{
    /**
     * @var string
     */
    public $firstname = null;

    /**
     * @var string
     */
    public $lastname = null;

    /**
     * @var string
     */
    public $phone = null;

    /**
     * @var string
     */
    public $ipaddress = null;

    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @var AbstractPayment
     */
    protected $payment = null;

    /**
     * @var BillingInterface
     */
    protected $imp = null;

    /**
     * @param BillingInterface $imp
     * @param array            $config
     */
    public function __construct(BillingInterface $imp, array $config = array())
    {
        $this->imp = $imp;

        if (!empty($config['address']) && $config['address'] instanceof Address) {
            $this->address = $config['address'];
        } else {
            $this->address = new Address();
        }

        // Default to credit card
        $this->payment = new CreditCard();
    }

    /**
     * @param Account $account
     *
     * @return Billing
     * @throws Exception
     */
    public function load(Account $account)
    {
        $this->clearProperties();
        $this->address->clearProperties();
        $this->payment = null;
        $this->account = $account;

        $this->imp->load($this);
        return $this;
    }

    /**
     * Save the current billing information.
     *
     * @return Billing
     * @throws Exception
     */
    public function save()
    {
        if (!$this->account || empty($this->account->code)) {
            throw new Exception('No account specified for Billing Info');
        }

        $this->setProperties(
            array(
                'firstname' => $this->firstname ? : $this->account->firstname,
                'lastname'  => $this->lastname  ? : $this->account->lastname,
                'ipaddress' => filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)
            )
        );

        // Copy account address if billing address is empty
        if (!array_filter($this->address->getProperties())) {
            $address = $this->account->address->getProperties();
            $this->address->setProperties($address);
        }

        try {
            $this->imp->save($this);
            $this->imp->load($this);

        } catch (NotFound $e) {
            // This is fine, accounts don't have to have billing info
        }
        return $this;
    }

    /**
     * Clear all billing information
     *
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        $this->imp->delete($this);
        $this->clearProperties();
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return Billing
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return AbstractPayment
     */
    public function getPayment()
    {
        if (!$this->payment) {
            $this->payment = new CreditCard();
        }
        return $this->payment;
    }

    /**
     * Set the payment information
     *
     * @param AbstractPayment|null $payment
     *
     * @return Billing
     */
    public function setPayment(AbstractPayment $payment = null)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @param array|object $data
     * @param array        $map
     *
     * @return Billing
     */
    public function setProperties($data, array $map = null)
    {
        parent::setProperties($data, $map);
        $this->address->setProperties($data, $map);

        if (is_object($data) && !empty($data->cc)) {
            $cc = $data->cc;
        } elseif (is_array($data) && !empty($data['cc'])) {
            $cc = $data['cc'];
        } else {
            $cc = $data;
        }

        if (empty($this->payment)) {
            $this->payment = new CreditCard($cc, $map);
        } else {
            $this->payment->setProperties($cc, $map);
        }

        return $this;
    }

    public function clearProperties($publicOnly = true)
    {
        parent::clearProperties($publicOnly);
        $this->address->clearProperties($publicOnly);
        $this->payment = null;

        return $this;
    }

    public function getPaymentType()
    {
        if (is_object($this->payment)) {
            $type = explode('\\', get_class($this->payment));
            return array_pop($type);
        }
        return null;
    }
}
