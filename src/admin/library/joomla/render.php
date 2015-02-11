<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive\Address;

defined('_JEXEC') or die();

/**
 * @TODO: Consider moving this to a new Simplerenew set of classes for display classes
 *
 * Class SimplerenewRender
 */
abstract class SimplerenewRender
{
    protected static $addressOrder = array(
        'address1', 'address2',
        'country', 'region',
        'city', 'postal'
    );

    /**
     * Get correct form field inputs for configured addressses
     *
     * @param string  $prefix
     * @param Address $address
     * @param mixed   $attribs
     * @param string  $requiredText
     *
     * @return array
     */
    public static function addressEdit(
        $prefix,
        Address $address,
        $attribs = array(),
        $requiredText = ' <span>*</span>'
    ) {
        $fields = array();
        if ($required = static::addressRequired()) {
            if (is_string($attribs)) {
                $attribs = JUtility::parseAttributes($attribs);
            }

            $required = array_intersect(static::addressFieldNames(), $required);
            foreach ($required as $fieldName) {
                $name  = $prefix . '[' . $fieldName . ']';
                $id    = $prefix . '_' . $fieldName;
                $value = $address->$fieldName;
                $label = 'COM_SIMPLERENEW_BILLING_' . $fieldName;

                $attribs = array_merge(
                    (array)$attribs,
                    array(
                        'data-msg-required' => JText::_('COM_SIMPLERENEW_VALIDATE_BILLING_' . $fieldName . '_REQUIRED')
                    )
                );
                if ($requiredText) {
                    $attribs['required'] = 'true';
                }

                switch ($fieldName) {
                    case 'address1':
                        if (!in_array('address2', $required)) {
                            $label                        = 'COM_SIMPLERENEW_BILLING_STREET';
                            $attribs['data-msg-required'] = JText::_(
                                'COM_SIMPLERENEW_VALIDATE_BILLING_STREET_REQUIRED'
                            );
                        }
                        $field = JHtml::_('sr.inputfield', $name, $attribs, $value, $id);
                        break;

                    case 'address2':
                        unset($attribs['required']);
                        $field = JHtml::_('sr.inputfield', $name, $attribs, $value, $id);
                        break;

                    case 'region':
                        $field = JHtml::_('srselect.region', $name, $attribs, $value, $id, $prefix . '_country');
                        break;

                    case 'country':
                        $field = JHtml::_('srselect.country', $name, $attribs, $value, $id);
                        break;

                    default:
                        $field = JHtml::_('sr.inputfield', $name, $attribs, $value, $id);
                        break;
                }

                $fields[] = '<label for="' . $id . '">'
                    . JText::_($label)
                    . (empty($attribs['required']) ? '' : $requiredText)
                    . '</label>'
                    . $field;
            }
        }
        return $fields;
    }

    /**
     * @TODO: This could be moved to the __string() method of the address object
     *
     * @param Address $address
     *
     * @return array
     */
    public static function addressDisplay(Address $address)
    {
        $fields   = array();
        $required = static::addressRequired();
        foreach ($required as $name) {
            if (isset($address->$name)) {
                if ($name == 'address1' && !in_array('address2', $required)) {
                    $label = 'COM_SIMPLERENEW_BILLING_STREET';
                } else {
                    $label = 'COM_SIMPLERENEW_BILLING_' . $name;
                }

                $fields[] = (object)array(
                    'label' => JText::_($label),
                    'value' => $address->$name
                );
            }
        }
        return $fields;
    }

    /**
     * Get the list of required address fields from configuration
     *
     * @return array
     */
    public static function addressRequired()
    {
        $params   = SimplerenewComponentHelper::getParams();
        $required = $params->get('basic.billingAddress');
        if ($required == 'none') {
            return array();
        }
        if (!$required) {
            return static::addressFieldNames();
        }
        return explode(',', $required);
    }

    /**
     * All address fields in the order we want to see them
     *
     * @return array
     */
    public static function addressFieldNames()
    {
        return static::$addressOrder;
    }
}
