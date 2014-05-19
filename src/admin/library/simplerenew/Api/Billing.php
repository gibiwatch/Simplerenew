<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Gateway\BillingInterface;

defined('_JEXEC') or die();

/**
 * Class Billing
 * @package Simplerenew\Api
 *
 * @property-read Account $account
 * @property-read string  $accountCode
 */
class Billing extends AbstractApiBase
{
    public $firstname = null;
    public $lastname = null;
    public $address1 = null;
    public $address2 = null;
    public $city = null;
    public $state = null;
    public $country = null;
    public $postal = null;
    public $phone = null;

    /**
     * @var BillingInterface
     */
    protected $imp = null;

    /**
     * @var Account
     */
    protected $account = null;

    public function __construct(Configuration $config, BillingInterface $imp)
    {
        parent::__construct($config);

        $this->imp = $imp;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'accountCode':
                if ($this->account) {
                    return $this->account->code;
                }
                break;

            case 'account':
                return $this->$name;
                break;
        }

        return parent::__get($name);
    }

    public function load(Account $account)
    {
        $keys      = array_keys($this->getProperties());
        $newValues = $this->imp->load($account->code, $keys);

        $this->account = $account;
        $this->setProperties($newValues);

        return $this;
    }
}
