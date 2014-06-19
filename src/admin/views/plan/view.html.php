<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $this->setTitle('Plan');

        SimplerenewToolbarHelper::apply('plan.apply');
        SimplerenewToolbarHelper::save('plan.save');

        $alt = $isNew ? '' : 'JTOOLBAR_CLOSE';
        SimplerenewToolbarHelper::cancel('plan.cancel', $alt);
    }
}
