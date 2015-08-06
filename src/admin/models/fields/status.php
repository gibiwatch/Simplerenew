<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class SimplerenewFormFieldStatus extends JFormFieldList
{
    public function getOptions()
    {
        $options = array(
            JHtml::_('select.option', Subscription::STATUS_ACTIVE, JText::_('COM_SIMPLERENEW_OPTION_STATUS_ACTIVE')),
            JHtml::_('select.option', Subscription::STATUS_CANCELED, JText::_('COM_SIMPLERENEW_OPTION_STATUS_CANCELED')),
            JHtml::_('select.option', Subscription::STATUS_EXPIRED, JText::_('COM_SIMPLERENEW_OPTION_STATUS_EXPIRED'))
        );

        return array_merge(parent::getOptions(), $options);
    }
}
