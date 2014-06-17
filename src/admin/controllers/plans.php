<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerPlans extends SimplerenewControllerAdmin
{
    protected $text_prefix = 'COM_SIMPLERENEW_PLANS';

    public function getModel($name = 'Plan', $prefix = 'SimplerenewModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function sync()
    {
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        /** @var SimplerenewModelPlans $plansModel */
        $plansModel = SimplerenewModel::getInstance('Plans', null, array('ignore_request' => true));
        $plansLocal = $plansModel->getItems();

        $plansGateway = SimplerenewHelper::getSimplerenew()->getPlan();
        $plansRemote  = $plansGateway->getList();

        $plansTable  = SimplerenewTable::getInstance('Plans');

        $plansDisable = array();
        $plansUpdate  = array();
        foreach ($plansLocal as $plan) {
            if (!array_key_exists($plan->code, $plansRemote)) {
                // Disable any plans not on the gateway
                if ($plan->published) {
                    $plansDisable[] = $plan->id;
                }
            } else {
                $plansUpdate[$plan->code] = $plan->id;
            }
        }

        if ($plansDisable) {
            /** @var SimplerenewModelPlan $planModel */
            $planModel = SimplerenewModel::getInstance('Plan');
            $planModel->publish($plansDisable, 0);
        }

        $errors = array();

        /** @var Simplerenew\Api\Plan $plan */
        foreach ($plansRemote as $code => $plan) {
            $plansTable->bind($plan->getProperties());
            if (array_key_exists($code, $plansUpdate)) {
                // Refresh old plan
                $plansTable->id = $plansUpdate[$code];
            } else {
                // Add new plan
                $plansTable->id        = null;
                $plansTable->published = 1;
            }

            if (!$plansTable->store()) {
                $errors = array_merge($errors, $plansTable->getErrors());
            }
        }

        // Set messaging for return
        if ($errors) {
            $message = join("\n", $errors);
            $type    = 'warning';
        } else {
            $message = array();
            if ($updated = count($plansUpdate)) {
                $message[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_UPDATED', $updated);
            }
            if ($added = count($plansRemote) - $updated) {
                $message[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_ADDED', $added);
            }
            if ($disabled = count($plansDisable)) {
                $message[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_DISABLED', $disabled);
            }
            if ($message) {
                $message = join("\n", $message);
            } else {
                $message = JText::_('COM_SIMPLERENEW_PLANS_NOSYNC');
            }
            $type = null;
        }
        $this->setRedirect('index.php?option=com_simplerenew&view=plans', $message, $type);
    }
}
