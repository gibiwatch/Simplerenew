<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewPlans extends SimplerenewAdminView
{
    /**
     * @var JObject
     */
    protected $state = null;

    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var JPagination
     */
    protected $pagination = null;

    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->setToolbar();
        parent::display($tpl);
    }

    protected function setToolbar()
    {
        $this->setTitle('COM_SIMPLERENEW_SUBMENU_PLANS');

        JToolBarHelper::addNew('instructor.add');
        JToolBarHelper::editList('instructor.edit');
        JToolBarHelper::deleteList(null, 'instructors.delete');

        parent::setToolBar();
    }
}
