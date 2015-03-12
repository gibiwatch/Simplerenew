<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

abstract class JHtmlSrselect
{
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

        $id = $idTag ? : str_replace(array('[', ']'), array('_', ''), $idTag);

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

        $id = $idTag ? : str_replace(array('[', ']'), array('_', ''), $name);

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
}
