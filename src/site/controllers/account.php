<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;
use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewControllerAccount extends SimplerenewControllerBase
{
    public function save()
    {
        $this->checkToken();

        /** @var SimplerenewModelGateway $model */
        $model = SimplerenewModel::getInstance('Gateway');
        $data  = new JRegistry($model->getState()->getProperties());

        $userId = $data->get('userId');
        $user   = SimplerenewFactory::getUser();

        // Check for authorisation
        if (!$user->id || $userId != $user->id) {
            $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_ACCOUNT_EDIT_NOAUTH'),
                'error'
            );
            return;
        }

        $container = SimplerenewFactory::getContainer();
        try {
            // Update User
            $user = $container->getUser()
                ->load($userId)
                ->setProperties($data->toArray())
                ->update();

            // Update Subscription account
            $account      = $container->getAccount();
            $billingToken = $data->get('billing.token');
            try {
                $account
                    ->load($user)
                    ->setProperties($data->toArray())
                    ->save(false);

            } catch (NotFound $e) {
                // Create an account only if they supplied a valid credit card
                if ($billingToken) {
                    $account
                        ->setUser($user)
                        ->setProperties($data->toArray())
                        ->save(true);
                }
            }

            // Update Billing
            if ($billingToken) {
                $container->billing
                    ->setAccount($account)
                    ->setProperties($data->get('billing'))
                    ->save($billingToken);

            } elseif ($account->code) {
                $app = SimplerenewFactory::getApplication();
                if ($app->input->getBool('clear_billing')) {
                    try {
                        $container->billing->load($account)->delete();
                    } catch (NotFound $e) {
                        // No billing to clear - that's cool
                    }

                } else {
                    try {
                        $billing = $container->billing->load($account);

                        $entered = (array)$data->get('billing.cc');
                        $current = $billing->payment->getProperties();
                        if (array_diff_assoc($entered, $current)) {
                            throw new Exception(JText::_('COM_SIMPLERENEW_ERROR_CCNUMBER_REQUIRED'));
                        }
                        $billing->setProperties($data->get('billing'))->save();

                    } catch (NotFound $e) {
                        // Not found, ignore this
                    }
                }
            }

        } catch (Exception $e) {
            $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_ACCOUNT_EDIT', $e->getMessage()),
                'error'
            );
            return;
        }

        $this->callerReturn(JText::_('COM_SIMPLERENEW_ACCOUNT_EDIT_SUCCESS'));
    }
}
