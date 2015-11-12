<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

abstract class JHtmlSrselect
{
    /**
     * @var array
     */
    protected static $plans = null;

    /**
     * @var array
     */
    protected static $groupedPlans = null;

    /**
     * Create a Credit Card expiration year dropdown
     *
     * @param string $name
     * @param mixed  $attribs
     * @param mixed  $selected
     * @param mixed  $idtag
     * @param bool   $translate
     *
     * @return mixed
     */
    public static function ccyear($name, $attribs = null, $selected = null, $idtag = false, $translate = false)
    {
        $now  = date('Y');
        $data = array();

        for ($i = $now; $i < ($now + 12); $i++) {
            $data[] = JHtml::_('select.option', $i, $i);
        }

        return JHtml::_('select.genericlist', $data, $name, $attribs, 'value', 'text', $selected, $idtag, $translate);
    }

    /**
     * Create a Credit Card expiration month dropdown
     *
     * @param string $name
     * @param mixed  $attribs
     * @param string $selected
     * @param mixed  $idtag
     * @param bool   $translate
     *
     * @return string
     */
    public static function ccmonth($name, $attribs = null, $selected = null, $idtag = false, $translate = false)
    {
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $date   = date('F', mktime(0, 0, 0, $i, 1));
            $data[] = JHtml::_('select.option', $i, $i . ' - ' . $date);
        }

        return JHtml::_('select.genericlist', $data, $name, $attribs, 'value', 'text', $selected, $idtag, $translate);
    }

    /**
     * Create country field as dropdown or text input depending
     * on existence of country list asset
     *
     * @param string $name
     * @param mixed  $attribs
     * @param string $selected
     * @param string $idTag
     *
     * @return string
     */
    public static function country($name, $attribs = null, $selected = null, $idTag = null)
    {
        JHtml::_('sr.jquery');

        $id = $idTag ?: str_replace(array('[', ']'), array('_', ''), $idTag);

        $countries = SimplerenewFactory::getDbo()
            ->setQuery('Select * From #__simplerenew_countries order By name')
            ->loadObjectList();
        if (count($countries)) {
            array_unshift(
                $countries,
                JHtml::_(
                    'select.option',
                    '',
                    JText::_('COM_SIMPLERENEW_OPTION_SELECT_COUNTRY'),
                    'code',
                    'name'
                )
            );
            return JHtml::_('select.genericlist', $countries, $name, $attribs, 'code', 'name', $selected, $id);
        }

        return JHtml::_('sr.inputfield', $name, $attribs, $selected, $id);
    }

    /**
     * Create a state/province dropdown and input field linked to a country
     * dropdown if region list asset exists
     *
     * @param string $name
     * @param mixed  $attribs
     * @param string $selected
     * @param string $idTag
     * @param string $countryId
     *
     * @return string
     */
    public static function region($name, $attribs = null, $selected = null, $idTag = null, $countryId = null)
    {
        JHtml::_('sr.jquery');

        $id = $idTag ?: str_replace(array('[', ']'), array('_', ''), $name);

        $html = array(
            JHtml::_('sr.inputfield', $name, $attribs, $selected, $id)
        );

        if ($countryId) {
            $regions = SimplerenewFactory::getDbo()
                ->setQuery('Select * From #__simplerenew_regions Order By name')
                ->loadObjectList();

            if (count($regions)) {
                $options = array();
                foreach ($regions as $region) {
                    $countryCode = $region->country_code;
                    if (!isset($options[$countryCode])) {
                        $options[$countryCode] = array(
                            (object)array('code' => '', 'name' => '')
                        );
                    }
                    $options[$countryCode][] = $region;
                }

                foreach ($options as $country => $regionOptions) {
                    $name   = $id . '_' . $country;
                    $html[] = JHtml::_(
                        'select.genericlist',
                        $regionOptions,
                        $name,
                        $attribs,
                        'code',
                        'name',
                        $selected
                    );
                }

                $jsonOptions = json_encode(
                    array(
                        'region'  => '#' . $id,
                        'country' => '#' . $countryId
                    )
                );
                JHtml::_('sr.onready', "$.Simplerenew.region({$jsonOptions});");
            }
        }
        return join("\n", $html);
    }

    /**
     * Create select box dropdown for plans. Use $required == false
     * to include a blank 'Select Plan' option.
     *
     * @param string       $name
     * @param string|array $attribs
     * @param string       $selected
     * @param bool         $required
     *
     * @return string
     */
    public static function plans($name, $attribs = null, $selected = null, $required = false)
    {
        $options = static::planoptions($required);
        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
    }

    /**
     * Create array of plans for use in option lists
     *
     * @param bool $required
     *
     * @return mixed
     */
    public static function planoptions($required = false)
    {
        if (static::$plans === null) {
            $db    = SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select(
                    array(
                        'plan.code AS ' . $db->quoteName('value'),
                        'CONCAT(plan.code, \' / \', plan.name) AS ' . $db->quoteName('text')
                    )
                )
                ->from('#__simplerenew_plans AS plan')
                ->innerJoin('#__simplerenew_subscriptions AS subscription ON subscription.plan = plan.code')
                ->group('plan.code, plan.name')
                ->order('plan.code ASC, plan.name ASC');

            static::$plans = $db->setQuery($query)->loadObjectList();
        }

        $plans = static::$plans;
        if (!$required) {
            array_unshift(
                $plans,
                JHtml::_('select.option', '', JText::_('COM_SIMPLERENEW_OPTION_SELECT_PLAN'))
            );
        }

        return $plans;
    }

    /**
     * @param string       $name
     * @param array|string $attribs
     * @param string       $selected
     * @param bool         $required
     *
     * @return string
     */
    public static function published($name, $attribs = null, $selected = null, $required = false)
    {
        $options = JHtml::_(
            'jgrid.publishedOptions',
            array(
                'archived' => false,
                'trash'    => false,
                'all'      => false
            )
        );

        if (!$required) {
            array_unshift(
                $options,
                JHtml::_(
                    'select.option',
                    '',
                    'COM_SIMPLERENEW_OPTION_SELECT_PUBLISHED'
                )
            );
        }

        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected, null, true);
    }

    /**
     * Create a select box dropdown for subscription status. Use $required == false
     * to include a blank 'Select status' option
     *
     * @param  string      $name
     * @param string|array $attribs
     * @param string       $selected
     * @param bool         $required
     *
     * @return string
     */
    public static function status($name, $attribs = null, $selected = null, $required = false)
    {
        $options = static::statusoptions($required);
        return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
    }

    /**
     * Create option list for subscription statuses
     *
     * @param bool $required
     *
     * @return array
     */
    public static function statusoptions($required = false)
    {
        if ($required) {
            $options = array();
        } else {
            $options = array(
                JHtml::_('select.option', '', JText::_('COM_SIMPLERENEW_OPTION_SELECT_STATUS'))
            );
        }
        $options = array_merge(
            $options,
            array(
                JHtml::_(
                    'select.option',
                    Subscription::STATUS_ACTIVE,
                    JText::_('COM_SIMPLERENEW_OPTION_STATUS_ACTIVE')
                ),
                JHtml::_(
                    'select.option',
                    Subscription::STATUS_EXPIRED,
                    JText::_('COM_SIMPLERENEW_OPTION_STATUS_EXPIRED')
                ),
                JHtml::_(
                    'select.option',
                    Subscription::STATUS_CANCELED,
                    JText::_('COM_SIMPLERENEW_OPTION_STATUS_CANCELED')
                )
            )
        );

        return $options;
    }

    /**
     * Create select dropdown for plans grouped by user group. Use $required == false
     * to include a blank 'Select Plan' option
     *
     * @param string       $name
     * @param string|array $attribs
     * @param string       $selected
     * @param bool         $required
     *
     * @return mixed
     */
    public static function groupedplans($name, $attribs = null, $selected = null, $required = false)
    {
        $plans = static::groupedplanoptions($required);

        $options = array(
            'list.attr'          => $attribs,
            'list.select'        => $selected,
            'group.items'        => null,
            'option.key.toHtml'  => false,
            'option.text.toHtml' => false
        );

        return JHtml::_('select.groupedlist', $plans, $name, $options);
    }

    /**
     * Build option list for plans grouped by user group. Use $required == false
     * to include a blank 'Select Plan' option
     *
     * @param bool $required
     *
     * @return array
     */
    public static function groupedplanoptions($required = false)
    {
        if (static::$groupedPlans === null) {
            $db    = SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select(
                    array(
                        $db->quoteName('group.title') . ' AS ' . $db->quoteName('group'),
                        $db->quoteName('plan.code'),
                        $db->quoteName('plan.name')
                    )
                )
                ->from('#__simplerenew_plans AS plan')
                ->innerJoin(
                    '#__usergroups AS ' . $db->quoteName('group')
                    . ' ON ' . $db->quoteName('group.id') . ' = plan.group_id'
                )
                ->order('group.title, plan.code, plan.name');

            static::$groupedPlans = array();

            $plans = $db->setQuery($query)->loadObjectList();
            foreach ($plans as $plan) {
                if (!isset(static::$groupedPlans[$plan->group])) {
                    static::$groupedPlans[$plan->group] = array();
                }
                static::$groupedPlans[$plan->group][] = JHtml::_(
                    'select.option',
                    $plan->code,
                    $plan->code . ' / ' . $plan->name
                );
            }
        }

        $plans = static::$groupedPlans;
        if (!$required) {
            array_unshift($plans, array(JText::_('COM_SIMPLERENEW_OPTION_SELECT_PLAN')));
        }
        return $plans;
    }
}
