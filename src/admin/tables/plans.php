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

    public function setGateway(Plan $plan)
    {
        $this->_gateway = $plan;
    }

    public function storeGateway()
    {
        if (!$this->_gateway instanceof Plan) {
            $this->_gateway = SimplerenewHelper::getSimplerenew()->getPlan();
        }

        $this->_gateway
            ->load($this->code)
            ->setProperties($this->getProperties());

        return $this->_gateway->save(true);
    }

    public function store($updateNulls = false)
    {
        if (trim($this->alias) == '') {
            $this->alias = $this->code;
        }

        $this->alias = SimplerenewApplicationHelper::stringURLSafe($this->alias);

        // Verify that the code and alias are unique
        $db = $this->getDbo();
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
