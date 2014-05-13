<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiBilling extends RecurlyApibase
{
    protected $classname = 'Recurly_BillingInfo';

    public function __construct($id = null)
    {
        if ($id != '' && is_numeric($id)) {
            $id = RecurlyApiAccount::getAccountCode($id);
        }
        parent::__construct($id);
    }

    /**
     * Billing Info is considered valid only if it is attached
     * to a account
     *
     * @return bool
     */
    public function isValid()
    {
        if (parent::isValid()) {
            return (bool)$this->recurly->account;
        }
        return false;
    }
}
