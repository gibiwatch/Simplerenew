<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewDashboard extends SimplerenewAdminView
{
    public function display($tpl = null)
    {
        $this->setToolBar();
        parent::display($tpl);
    }

    protected function setToolbar()
    {
        $this->setTitle();
        parent::setToolBar(false);
    }
}
