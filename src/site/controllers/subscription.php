<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

/**
 * Process non-Credit Card subscription/change forms
 *
 * Class SimplerenewControllerSubscription
 */
class SimplerenewControllerSubscription extends SimplerenewControllerBase
{
    public function display($cachable = false, $urlparams = array())
    {
        $app = SimplerenewFactory::getApplication();
        $app->enqueueMessage(
            JText::sprintf('COM_SIMPLERENEW_ERROR_UNKNOWN_TASK', $this->getTask()),
            'error'
        );
    }

    public function create()
    {
        $this->checkToken();

        SimplerenewHelper::saveFormData(
            'subscribe.create',
            array(
                'password',
                'password2',
                'billing.cc.number',
                'billing.cc.cvv'
            )
        );

        $app      = SimplerenewFactory::getApplication();

        $method   = $app->input->getCmd('payment_method');
        if ($method == 'cc') {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_NO_CREDITCARD'),
                'error'
            );
            return;
        }

        $planCode = $app->input->getString('planCode');
        if (!$planCode) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_NOPLAN_SELECTED'),
                'error'
            );
            return;
        }

        $this->callerReturn(
            'Payment via PayPal is not yet implemented',
            'error'
        );
    }

    public function change()
    {
        $this->checkToken();

        $app = SimplerenewFactory::getApplication();

        $id  = $app->input->getString('id');
        if (!$id) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_SUBSCRIPTION_NOID'),
                'error'
            );
            return;
        }

        $method = $app->input->getCmd('payment_method');
        if ($method == 'cc') {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_NO_CREDITCARD'),
                'error'
            );
            return;
        }

        $this->callerReturn(
            'Payment via PayPal is not yet implemented',
            'error'
        );
    }
}
