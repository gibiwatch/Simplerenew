<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

abstract class SimplerenewControllerAdmin extends JControllerAdmin
{
    protected function checkToken()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
    }
}
