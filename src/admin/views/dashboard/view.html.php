<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewDashboard extends SimplerenewViewAdmin
{
    public function display($tpl = null)
    {
        $this->setToolBar();
        parent::display($tpl);
    }

    protected function setToolbar($addDivider = false)
    {
        $this->setTitle();
        SimplerenewHelper::addSubmenu('dashboard');

        parent::setToolBar($addDivider);
    }
}
