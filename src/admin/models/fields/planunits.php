<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Plan;

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class JFormFieldPlanunits extends JFormFieldList
{
    protected $predefinedOptions = array(
        Plan::INTERVAL_DAYS   => 'COM_SIMPLERENEW_OPTION_DAYS',
        Plan::INTERVAL_WEEKS  => 'COM_SIMPLERENEW_OPTION_WEEKS',
        Plan::INTERVAL_MONTHS => 'COM_SIMPLERENEW_OPTION_MONTHS',
        Plan::INTERVAL_YEARS  => 'COM_SIMPLERENEW_OPTION_YEARS'
    );

    public function getOptions()
    {
        $options = parent::getOptions();

        foreach ($this->predefinedOptions as $value => $text) {
            $options[] = JHtml::_('select.option', $value, JText::_($text));
        }

        return $options;
    }


}
