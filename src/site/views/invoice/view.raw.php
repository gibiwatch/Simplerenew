<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewInvoice extends SimplerenewViewSite
{
    /**
     * @var User
     */
    protected $user = null;

    public function display($tpl = null)
    {
        /** @var SimplerenewModelInvoice $model */
        $model = $this->getModel();

        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                // @TODO: redirect required
            }

            $invoice = $model->getInvoice();
            $fileName = 'invoice_' . $invoice->number . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename=' . $fileName);
            echo $invoice->toPDF();
            jexit();

        } catch (Simplerenew\Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
    }
}
