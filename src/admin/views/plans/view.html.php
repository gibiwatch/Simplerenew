<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewPlans extends SimplerenewViewAdmin
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
     * @var JForm
     */
    public $filterForm = null;

    /**
     * @var array
     */
    public $activeFilters = null;

    /**
     * @var JPagination
     */
    protected $pagination = null;

    public function display($tpl = null)
    {
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->pagination    = $this->get('Pagination');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->setToolbar();

        $notices = SimplerenewHelper::getNotices();
        SimplerenewHelper::enqueueMessages($notices);

        parent::display($tpl);
    }

    protected function setToolbar($addDivider = true)
    {
        SimplerenewHelper::addSubmenu('plans');

        $this->setTitle('COM_SIMPLERENEW_SUBMENU_PLANS');

        SimplerenewToolbarHelper::addNew('plan.add');
        SimplerenewToolbarHelper::editList('plan.edit');
        SimplerenewToolbarHelper::publish('plans.publish', 'JTOOLBAR_PUBLISH', true);
        SimplerenewToolbarHelper::unpublish('plans.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        SimplerenewToolbarHelper::deleteList('COM_SIMPLERENEW_PLAN_DELETE_CONFIRM', 'plans.delete');

        parent::setToolBar($addDivider);
    }

    protected function displayFooter()
    {
        parent::displayFooter();

        $update = SimplerenewFactory::getStatus()->update;
        if ($update) {
            $link = 'index.php?option=com_simplerenew&task=update.update';
            echo JText::sprintf('COM_SIMPLERENEW_UPDATE_AVAILABLE', $link, $update->version);
        }
    }
}
