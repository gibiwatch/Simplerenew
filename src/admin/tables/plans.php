<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Plan;
use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

class SimplerenewTablePlans extends SimplerenewTable
{
    /**
     * @var Plan
     */
    protected $_gateway = null;

    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__simplerenew_plans', 'id', $db);
    }

    public function getGateway()
    {
        if (!$this->_gateway instanceof Plan) {
            $this->_gateway = SimplerenewFactory::getContainer()->getPlan();
        }
        return $this->_gateway;
    }

    public function setGateway(Plan $plan)
    {
        $this->_gateway = $plan;
    }

    public function storeGateway()
    {
        $plan = $this->getGateway();

        try {
            $plan->load($this->code);
        } catch (NotFound $e) {
            // The safest thing to do here is create a new local plan
            $this->id = null;
        }

        $plan->setProperties($this->getProperties());
        return $plan->save();
    }

    public function delete($pk = null)
    {
        if ($pk) {
            $localPlan = $this;
        } else {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($this->_tbl)
                ->where('id = ' . $pk);

            $localPlan = $db->setQuery($query)->loadObject();
        }

        $success = parent::delete($pk);
        if ($success && $localPlan) {
            try {
                $remotePlan = $this->getGateway()->load($localPlan->code);
                $remotePlan->delete();

                SimplerenewFactory::getApplication()
                    ->enqueueMessage(
                        JText::sprintf(
                            'COM_SIMPLERENEW_PLAN_GATEWAY_REMOVE',
                            $localPlan->code
                        )
                    );
                return true;

            } catch (NotFound $e) {
                // Nothing on the gateway to delete

            } catch (Exception $e) {
                SimplerenewFactory::getApplication()->enqueueMessage(
                    JText::sprintf(
                        'COM_SIMPLERENEW_ERROR_PLAN_GATEWAY_REMOVE',
                        $localPlan->code,
                        $e->getMessage()
                    ),
                    'error'
                );
                return true;
            }
        }
        return $success;
    }

    public function publish($pks = null, $state = 1, $userId = 0)
    {
        if ($state) {
            // Review plans to be published to be sure they already exist on the Gateway

            if ($pks) {
                $ids   = array_filter(array_unique((array)$pks));
                $db    = $this->getDbo();
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__simplerenew_plans')
                    ->where('id IN (' . join(',', $ids) . ')');

                $localPlans = $db->setQuery($query)->loadObjectList();

            } elseif ($this->id) {
                $localPlans = array($this);
            }

            if (!empty($localPlans)) {
                $remotePlan = $this->getGateway();
                foreach ($localPlans as $plan) {
                    try {
                        $remotePlan->load($plan->code);
                    } catch (Exception $e) {
                        // Plan doesn't exist on gateway, we'll just create it
                        try {
                            $remotePlan->setProperties($plan)->save();

                            SimplerenewFactory::getApplication()
                                ->enqueueMessage(
                                    JText::sprintf(
                                        'COM_SIMPLERENEW_PLAN_GATEWAY_CREATE',
                                        $plan->code
                                    )
                                );

                        } catch (Exception $e) {
                            SimplerenewFactory::getApplication()->enqueueMessage(
                                JText::sprintf(
                                    'COM_SIMPLERENEW_ERROR_PLAN_GATEWAY_CREATE',
                                    $plan->code,
                                    $e->getMessage()
                                ),
                                'error'
                            );
                            return false;
                        }
                    }
                }
            }
        }
        return parent::publish($pks, $state, $userId);
    }

    public function store($updateNulls = false)
    {
        if (empty($this->code)) {
            $this->code = $this->name;
        }
        if (empty($this->id)) {
            $this->code = SimplerenewApplicationHelper::stringURLSafe($this->code);
        }

        // Verify that the code is unique
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from($this->_tbl);

        if ($this->id) {
            $query->where('id <> ' . $db->quote($this->id));
        }
        $query->where('code = ' . $db->quote($this->code));

        if ($db->setQuery($query)->loadResult() > 0) {
            $this->setError(JText::_('COM_SIMPLERENEW_ERROR_PLAN_DUPLICATE'));
            return false;
        }

        if (!$this->storeGateway()) {
            $this->setError(JText::_('COM_SIMPLERENEW_ERROR_PLAN_GATEWAY_SAVE'));
            return false;
        }

        return parent::store($updateNulls);
    }
}
