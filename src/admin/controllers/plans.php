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
    public function getModel($name = 'Plan', $prefix = 'SimplerenewModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function sync()
    {
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        /** @var SimplerenewModelPlans $plansModel */
        $plansModel = SimplerenewModel::getInstance('Plans');
        $plansModel->getState()->setProperties(
            array(
                'list.start'    => 0,
                'list.limit'    => 0,
                'filter.search' => ''
            )
        );

        $plansGateway = SimplerenewHelper::getSimplerenew()->getPlan();

        $plansLocal  = $plansModel->getItems();
        $plansRemote = $plansGateway->getList();

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

        $table  = SimplerenewTable::getInstance('Plans');
        $errors = array();

        /** @var Simplerenew\Api\Plan $plan */
        foreach ($plansRemote as $code => $plan) {
            $table->bind($plan->getProperties());
            if (array_key_exists($plan->code, $plansUpdate)) {
                $table->id = $plansUpdate[$plan->code];
            } else {
                $table->id        = null;
                $table->published = 1;
            }

            if (!$table->store()) {
                $errors = array_merge($errors, $table->getErrors());
            }
        }

        if ($errors) {
            $message = join("\n", $errors);
            $type    = 'warning';
        } else {
            $updated  = count($plansUpdate);
            $added    = count($plansRemote) - $updated;
            $disabled = count($plansDisable);
            $message  = JText::sprintf('COM_SIMPLERENEW_PLANS_SYNCED', $added, $updated, $disabled);
            $type     = null;
        }
        $this->setRedirect('index.php?option=com_simplerenew&view=plans', $message, $type);
    }
}
