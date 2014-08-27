<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
            return $this->callerReturn(
                JText::_('COM_SIMPLERENEW_ERROR_ACCOUNT_EDIT_NOAUTH'),
                'error'
            );
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
                // Create an account only if they supplied a credit card
                if ($billingToken) {
                    $account
                        ->setUser($user)
                        ->setProperties($data->toArray())
                        ->save(true);
                }
            }

            // Update Billing
            if ($account->status === Account::STATUS_ACTIVE) {
                $container->getBilling()->setAccount($account)
                    ->setProperties($data->get('billing'))
                    ->save($billingToken);
            }

        } catch (Exception $e) {
            return $this->callerReturn(
                JText::sprintf('COM_SIMPLERENEW_ERROR_ACCOUNT_EDIT', $e->getMessage()),
                'error'
            );
        }

        $this->callerReturn(JText::_('COM_SIMPLERENEW_ACCOUNT_EDIT_SUCCESS'));
    }
}
