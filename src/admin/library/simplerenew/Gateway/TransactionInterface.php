<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Api\Transaction;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface TransactionInterface
{
    /**
     * Retrieve a specific Transaction
     *
     * @param Transaction $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Transaction $parent);

    /**
     * Get list of transactions for account
     *
     * @param Transaction $template
     * @param Account     $account
     * @param int         $statusMask Bitmask of Transaction statuses to return
     *
     * @return array
     */
    public function getList(Transaction $template, Account $account, $statusMask = null);
}
