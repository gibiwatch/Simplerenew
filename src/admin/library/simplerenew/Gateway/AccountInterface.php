<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Account;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface AccountInterface
{
    /**
     * Retrieve basic account information from the subscription gateway.
     * The parent Account class properties will be set on success
     *
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Account $parent);

    /**
     * Save account data using current settings.
     * Expected to handle creation and updating of accounts
     * and reset account values for updated/created account.
     *
     * @param Account $parent
     * @param bool    $isNew
     *
     * @return void
     * @throws Exception
     */
    public function save(Account $parent, $isNew);

    /**
     * Close/Inactivate the account
     *
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function close(Account $parent);

    /**
     * Reopen the previously closed account
     *
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function reopen(Account $parent);
}
