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
     * @var JForm
     */
    public $form = null;

    public function display($tpl = null)
    {
        $this->form = $this->getForm();

        $this->setToolBar();
        parent::display($tpl);
    }

    protected function setToolBar($addDivider = true)
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $this->setTitle('Plan');

        SimplerenewToolbarHelper::save('plan.save');
        SimplerenewToolbarHelper::apply('plan.apply');
        SimplerenewToolbarHelper::cancel('plan.cancel');
    }
}
