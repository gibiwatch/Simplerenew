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
        $allOptions = array(
            Subscription::STATUS_ACTIVE   => JText::_('COM_SIMPLERENEW_OPTION_STATUS_ACTIVE'),
            Subscription::STATUS_CANCELED => JText::_('COM_SIMPLERENEW_OPTION_STATUS_CANCELED'),
            Subscription::STATUS_EXPIRED  => JText::_('COM_SIMPLERENEW_OPTION_STATUS_EXPIRED')
        );

        $list = array_filter(explode(',', (string)$this->element['exclude']));
        $excludes = array();
        foreach ($list as $exclude) {
            $key = '\Simplerenew\Api\Subscription::STATUS_' . strtoupper($exclude);
            if (defined($key)) {
                $excludes[] = constant($key);
            }
        }

        $options = array();
        foreach ($allOptions as $value => $text) {
            if (!in_array($value, $excludes)) {
                $options[] = JHtml::_('select.option', $value, $text);
            }
        }
        return array_merge(parent::getOptions(), $options);
    }
}
