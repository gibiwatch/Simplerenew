<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewPlan extends SimplerenewViewAdmin
{
    /**
     * @var object
     */
    protected $item = null;

    /**
     * @var JForm
     */
    public $form = null;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $this->setToolBar();
        parent::display($tpl);
    }

    protected function setToolBar($addDivider = true)
    {
        $isNew = ($this->item->id == 0);
        SimplerenewFactory::getApplication()->input->set('hidemainmenu', true);

        $title = 'COM_SIMPLERENEW_PAGE_VIEW_PLAN_' . ($isNew ? 'ADD' : 'EDIT');
        $this->setTitle($title);

        SimplerenewToolbarHelper::apply('plan.apply');
        SimplerenewToolbarHelper::save('plan.save');

        $alt = $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE';
        SimplerenewToolbarHelper::cancel('plan.cancel', $alt);
    }
}
