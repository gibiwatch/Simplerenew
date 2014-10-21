<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewModelPlan extends SimplerenewModelAdmin
{
    public function getTable($type = 'Plans', $prefix = 'SimplerenewTable', $config = array())
    {
        return SimplerenewTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_simplerenew.plan', 'plan', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = SimplerenewFactory::getApplication()->getUserState('com_simplerenew.edit.plan.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Load some defaults for new plans
            if (!$data->get('id')) {
                $gid = SimplerenewComponentHelper::getParams()->get('basic.defaultGroup');
                $data->set('group_id', $gid);
            }

            // We are supporting only a single currency at this time
            if (!$data->get('currency')) {
                $db = SimplerenewFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select('currency')
                    ->from('#__simplerenew_plans')
                    ->where('currency != ' . $db->quote(''));
                $currency = $db->setQuery($query, 0, 1)->loadResult();
                $data->set('currency', $currency);
            }
        }

        return $data;
    }
}
