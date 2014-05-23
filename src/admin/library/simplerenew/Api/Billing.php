<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Gateway\BillingInterface;
use Simplerenew\Prototype\Address;

defined('_JEXEC') or die();

/**
 * Class Billing
 * @package Simplerenew\Api
 *
 * @property-read Account $account
 * @property-read Address $address
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
     * @var BillingInterface
     */
    private $imp = null;

    /**
     * @param BillingInterface $imp
     * @param array            $config
     */
    public function __construct(BillingInterface $imp, array $config = array())
    {
        $this->imp = $imp;

        if (!empty($config['address']) && $config['address'] instanceof Address) {
            $this->address = clone $config['address'];
        } else {
            $this->address = new Address();
        }
    }

    /**
     * @param Account $account
     *
     * @return $this
     */
    public function load(Account $account)
    {
        $this->clearProperties();
        $this->address->clearProperties();

        $this->account = $account;

        $this->imp->load($this);

        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}
