<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Plan;

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
            $this->_gateway = SimplerenewHelper::getSimplerenew()->getPlan();
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

        $plan
            ->load($this->code)
            ->setProperties($this->getProperties());

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

        $success = true; //parent::delete($pk);
        if ($success && $localPlan) {
            $remotePlan = $this->getGateway()->load($localPlan->code);
            try {
                $remotePlan->delete();

                SimplerenewFactory::getApplication()
                    ->enqueueMessage(
                        JText::sprintf(
                            'COM_SIMPLERENEW_PLAN_GATEWAY_REMOVE',
                            $localPlan->code
                        )
                    );
                return false;

            } catch (Exception $e) {
                $this->setError(
                    JText::sprintf(
                        'COM_SIMPLERENEW_ERROR_PLAN_GATEWAY_REMOVE',
                        $localPlan->code,
                        $e->getMessage()
                    )
                );
                return false;
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
                    $remotePlan->load($plan->code);
                    if (!$remotePlan->created) {
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
                            $this->setError(
                                JText::sprintf(
                                    'COM_SIMPLERENEW_ERROR_PLAN_GATEWAY_CREATE',
                                    $plan->code,
                                    $e->getMessage()
                                )
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
        if (trim($this->alias) == '') {
            $this->alias = $this->code;
        }

        $this->alias = SimplerenewApplicationHelper::stringURLSafe($this->alias);

        // Verify that the code and alias are unique
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from($this->_tbl);

        if ($this->id) {
            $query->where('id <> ' . $db->quote($this->id));
        }
        $query->where(
            '('
            . join(
                ' OR ',
                array(
                    'alias = ' . $db->quote($this->alias),
                    'code = ' . $db->quote($this->code)
                )
            )
            . ')'
        );

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
