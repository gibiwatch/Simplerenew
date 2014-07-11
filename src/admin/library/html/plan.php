<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlPlan
{
    /**
     * Return a standard format plan name
     *
     * @param array|object $plan
     *
     * @return string
     */
    public static function name($plan)
    {
        if (is_array($plan)) {
            $plan = (object)$plan;
        }

        if (empty($plan->name)) {
            return '';
        }

        $text = $plan->name . ' ' . number_format($plan->amount, 2);
        if (isset($plan->trial_length) && $plan->trial_length > 0) {
            $text .= ' - '
                . JText::sprintf(
                    'COM_SIMPLERENEW_TRIAL_' . strtoupper($plan->trial_unit),
                    $plan->trial_length
                );
        }
        return $text;
    }
}
