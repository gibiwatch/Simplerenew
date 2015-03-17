<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
