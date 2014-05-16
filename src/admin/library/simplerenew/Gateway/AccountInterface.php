<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

defined('_JEXEC') or die();

interface AccountInterface
{
    /**
     * Retrieve basic account information from the subscription gateway.
     * Fields to retrieve and their new values are passed through $data
     *
     * @param string $accountCode
     * @param array  $data
     *
     * @return void
     * @throws \Simplerenew\Exception
     */
    public function load($accountCode, array &$data);
}
