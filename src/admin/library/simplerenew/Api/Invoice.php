<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
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

    public function __construct(Configuration $config, InvoiceInterface $imp)
    {
        parent::__construct();

        $this->imp = $imp;
    }

    /**
     * Load an invoice by number
     *
     * @param string $number
     *
     * @return Invoice
     * @throws Exception
     */
    public function load($number)
    {
        $this->clearProperties();

        $this->number = $number;
        $this->imp->load($this);

        return $this;
    }

    /**
     * Return all invoices for the selected account
     *
     * @param Account $account
     *
     * @return array
     * @throws Exception
     */
    public function getAccountList(Account $account)
    {
        return $this->imp->getAccountList($this, $account);
    }

    public function toPDF()
    {
        return $this->imp->toPDF($this);
    }
}
