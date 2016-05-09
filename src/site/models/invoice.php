<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once __DIR__ . '/account.php';

class SimplerenewModelInvoice extends SimplerenewModelAccount
{
    public function getInvoice()
    {
        $invoice = $this->getState('invoice');
        if (!$invoice instanceof Simplerenew\Api\Invoice) {
            $account   = $this->getAccount();
            $container = SimplerenewFactory::getContainer($account->getGatewayName());

            $number  = $this->getState('invoice.number');
            $invoice = $container->getInvoice()->load($number);
            if ($invoice->account_code != $account->code) {
                throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_INVOICE_NOAUTH'), 401);
            }

            $this->setState('invoice', $invoice);
        }

        return $invoice;
    }

    protected function populateState()
    {
        parent::populateState();

        $number = SimplerenewFactory::getApplication()->input->getString('number');
        $this->setState('invoice.number', $number);
    }
}
