<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends JControllerLegacy
{
    public function display()
    {
        $input = JFactory::getApplication()->input;

        $view = $input->getCmd('view', $this->default_view);
        SimplerenewHelper::addSubmenu($view);

        parent::display();
    }
}
