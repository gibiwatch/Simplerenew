<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewDispatcher extends FOFDispatcher
{
    public function onBeforeDispatch()
    {
        FOFTemplateUtils::addCSS('media://com_simplerenew/css/backend.css');
        return true;
    }
}
