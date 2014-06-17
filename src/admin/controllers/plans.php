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

        $plansLocal = $plansModel->getItems();
        $plansRemote = $plansGateway->getList();

        // Add new plans from the gateway
        foreach ($plansRemote as $code => $plan) {

        }

    }
}
