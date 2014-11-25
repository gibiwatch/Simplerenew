<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Api\Account;
use Simplerenew\Exception;
use Simplerenew\Gateway\InvoiceInterface;

defined('_JEXEC') or die();

class Invoice extends AbstractApiBase
{
    const STATUS_OPEN     = 1;
    const STATUS_PAID     = 2;
    const STATUS_PAST_DUE = 3;
    const STATUS_UNKNOWN  = 0;

    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $number = null;

    /**
     * @var \DateTime
     */
    public $date = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var string
     */
    public $po_number = null;

    /**
     * @var string
     */
    public $coupon = null;

    /**
     * @var float
     */
    public $subtotal = null;

    /**
     * @var float
     */
    public $tax = null;

    /**
     * @var float
     */
    public $total = null;

    /**
     * @var string
     */
    public $currency = null;

    /**
     * @var string
     */
    public $account_code = null;

    /**
     * @var string
     */
    public $subscription_id = null;

    /**
     * @var InvoiceInterface
     */
    protected $imp = null;

    /**
     * @param InvoiceInterface $imp
     * @param array                 $config
     */
    public function __construct(InvoiceInterface $imp, array $config = array())
    {
        $this->imp = $imp;
    }

    /**
     * Load an invoice by system ID
     *
     * @param string $id
     *
     * @return Invoice
     * @throws Exception
     */
    public function load($id)
    {
        $this->clearProperties();

        $this->id = $id;
        $this->imp->load($this);

        return $this;
    }

    public function getAccountList(Account $account)
    {
        return $this->imp->getAccountList($this, $account);
    }
}
