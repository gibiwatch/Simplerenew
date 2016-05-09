<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Api\Invoice;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface InvoiceInterface
{
    /**
     * @param Invoice $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Invoice $parent);

    /**
     * Return all invoices for the selected account
     *
     * @param Invoice $template
     * @param Account $account
     *
     * @return array
     * @throws Exception
     */
    public function getAccountList(Invoice $template, Account $account);

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Invoice $parent
     * @param mixed   $data
     *
     * @return void
     */
    public function bindSource(Invoice $parent, $data);

    /**
     * Return an invoice as pdf
     * @param Invoice $parent
     *
     * @return string
     * @throws Exception
     */
    public function toPDF(Invoice $parent);
}
