<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerJson extends JControllerLegacy
{
    /**
     * Standard token checking for json controllers
     *
     * @return void
     * @throws Exception
     */
    protected function checkToken()
    {
        if (!JSession::checkToken()) {
            throw new Exception(JText::_('JINVALID_TOKEN'), 403);
        }
    }
}
