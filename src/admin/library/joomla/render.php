<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Primitive\Address;

defined('_JEXEC') or die();

/**
 * @TODO: Consider moving this to a new Simplerenew set of classes for display classes
 *
 * Class SimplerenewFields
 */
abstract class SimplerenewRender
{
    public static function addressEdit($prefix, Address $address, $attribs = array())
    {
        $fields = array();
        if ($required = self::addressRequired()) {
            if (is_string($attribs)) {
                $attribs = JUtility::parseAttributes($attribs);
            }

            $required = array_intersect(self::addressFieldNames(), $required);
            foreach ($required as $name) {
                $id    = $prefix . '_' . $name;
                $label = 'COM_SIMPLERENEW_BILLING_' . $name;

                $attribs = array_merge(
                    $attribs,
                    array(
                        'id'       => $id,
                        'name'     => $prefix . '[' . $name . ']',
                        'type'     => 'text',
                        'value'    => $address->$name,
                        'required' => 'true'
                    )
                );

                switch ($name) {
                    case 'address1':
                        if (!in_array('address2', $required)) {
                            $label = 'COM_SIMPLERENEW_BILLING_STREET';
                        }
                        $field = '<input ' . JArrayHelper::toString($attribs) . '/>';
                        break;

                    case 'address2':
                        unset($attribs['required']);
                        $field = '<input ' . JArrayHelper::toString($attribs) . '/>';
                        break;

                    default:
                        $field = '<input ' . JArrayHelper::toString($attribs) . '/>';
                        break;
                }

                $fields[] = '<label for="' . $id . '">'
                    . JText::_($label)
                    . '</label>'
                    . $field;
            }
        }
        return $fields;
    }

    public static function addressDisplay(Address $address)
    {
        $fields = array();
        $required = self::addressRequired();
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

    public static function addressRequired()
    {
        $params   = SimplerenewComponentHelper::getParams();
        $required = $params->get('account.billingAddress');
        if ($required == 'none') {
            return array();
        }
        if (!$required) {
            return self::addressFieldNames();
        }
        return explode(',', $required);
    }

    public static function addressFieldNames()
    {
        return array('address1', 'address2', 'city', 'region', 'postal', 'country');
    }
}
