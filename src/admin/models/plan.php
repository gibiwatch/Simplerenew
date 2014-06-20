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
        }
        return $data;
    }
}
