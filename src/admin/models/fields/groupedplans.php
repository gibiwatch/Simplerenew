<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

JFormHelper::loadFieldClass('GroupedList');

class SimplerenewFormFieldGroupedplans extends JFormFieldGroupedList
{
    public function getGroups()
    {
        $groups = JHtml::_('srselect.groupedplanoptions', true);

        return array_merge_recursive(parent::getGroups(), $groups);
    }
}
