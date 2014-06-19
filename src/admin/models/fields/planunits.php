<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Plan;

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('PredefinedList');

class JFormFieldPlanunits extends JFormFieldPredefinedList
{
    protected $predefinedOptions = array(
        Plan::INTERVAL_DAYS   => 'COM_SIMPLERENEW_OPTION_DAYS',
        Plan::INTERVAL_WEEKS  => 'COM_SIMPLERENEW_OPTION_WEEKS',
        Plan::INTERVAL_MONTHS => 'COM_SIMPLERENEW_OPTION_MONTHS',
        Plan::INTERVAL_YEARS  => 'COM_SIMPLERENEW_OPTION_YEARS'
    );


}
