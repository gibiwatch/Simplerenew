<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

class SimplerenewViewInvoices extends SimplerenewViewSite
{
    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var array
     */
    protected $invoices = array();

    public function display($tpl = null)
    {
        /** @var SimplerenewModelInvoices $model */
        $model = $this->getModel();

        try {
            $this->user = $model->getUser();
            if (!$this->user) {
                $this->setLayout('login');
            }
            $this->invoices = $model->getInvoices();

        } catch (Simplerenew\Exception $e) {
            SimplerenewFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }
}
