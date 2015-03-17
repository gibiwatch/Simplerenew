<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

JFormHelper::loadFieldClass('List');

class JFormFieldCurrency extends JFormFieldList
{
    public function getInput()
    {
        // Currency cannot be changed once it's been set
        // @TODO: This will require refactoring when we start accepting multiple currencies
        if ($this->value) {
            $attribs = array(
                'type'  => 'hidden',
                'name'  => $this->name,
                'id'    => $this->id,
                'value' => $this->value
            );

            $html = '<input ' . SimplerenewUtilitiesArray::toString($attribs) . '/>';

            $show = (string)$this->element['show'];
            if ($show !== 'false' && $show !== '0') {
                $html .= $this->value;
            }

            return $html;
        }

        return parent::getInput();
    }

    public function getLabel()
    {
        if ($this->value) {
            return '';
        }

        return parent::getLabel();
    }

    public function getOptions()
    {
        return array_merge(parent::getOptions(), JHtml::_('currency.options'));
    }
}
