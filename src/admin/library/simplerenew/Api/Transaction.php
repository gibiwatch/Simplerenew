<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Gateway\TransactionInterface;

defined('_JEXEC') or die();

class Transaction extends AbstractApiBase
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED  = 2;
    const STATUS_VOID    = 4;
    const STATUS_UNKNOWN = 0;

    const ACTION_PURCHASE = 1;
    const ACTION_AUTH     = 2;
    const ACTION_REFUND   = 4;
    const ACTION_UNKNOWN  = 0;

    const METHOD_CARD        = 1;
    const METHOD_PAYPAL      = 2;
    const METHOD_CHECK       = 4;
    const METHOD_WIRE        = 8;
    const METHOD_MONEY_ORDER = 16;
    const METHOD_UNKNOWN     = 0;

    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $accountCode = null;

    /**
     * @var string
     */
    public $invoiceNumber = null;

    /**
     * @var string
     */
    public $subscriptionId = null;

    /**
     * @var string
     */
    public $reference = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var int
     */
    public $action = null;

    /**
     * @var float
     */
    public $amount = null;

    /**
     * @var float
     */
    public $tax = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var int
     */
    public $method = null;

    /**
     * @var bool
     */
    public $recurring = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var TransactionInterface
     */
    protected $imp = null;

    /**
     * @param Configuration        $config
     * @param TransactionInterface $imp
     */
    public function __construct(Configuration $config, TransactionInterface $imp)
    {
        parent::__construct();

        $this->imp = $imp;
    }

    /**
     * Load a transaction by system id
     *
     * @param string $id
     *
     * @return Transaction
     * @throws Exception
     */
    public function load($id)
    {
        $this->clearProperties();

        $this->id = $id;
        $this->imp->load($this);

        return $this;
    }

    /**
     * Get list of transaction for the selected account
     *
     * @param Account $account
     * @param int     $statusMask Bitmask of Transaction status codes to retrieve
     *
     * @return array()
     */
    public function getList(Account $account, $statusMask = null)
    {
        return $this->imp->getList($this, $account, $statusMask);
    }

}
