<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Account;

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class SimplerenewFormFieldAccountstatus extends JFormFieldList
{
    public function getOptions()
    {
        $options = array(
            JHtml::_('select.option', Account::STATUS_ACTIVE, JText::_('COM_SIMPLERENEW_OPTION_STATUS_ACTIVE')),
            JHtml::_('select.option', Account::STATUS_CLOSED, JText::_('COM_SIMPLERENEW_OPTION_STATUS_CLOSED'))
        );

        return array_merge(parent::getOptions(), $options);
    }
}
