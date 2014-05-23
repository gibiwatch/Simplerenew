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
     * @var BillingInterface
     */
    private $imp = null;

    public function __construct(BillingInterface $imp)
    {
        $this->imp = $imp;
    }

    /**
     * @param Account $account
     *
     * @return $this
     */
    public function load(Account $account)
    {
        $keys      = array_keys($this->getProperties());
        $newValues = $this->imp->load($account->code, $keys);

        $this->account = $account;
        $this->setProperties($newValues);

        return $this;
    }
}
