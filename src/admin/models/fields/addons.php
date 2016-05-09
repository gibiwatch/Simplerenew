<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

JFormHelper::loadFieldClass('Text');

class JFormFieldAddons extends JFormFieldText
{

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $html = array();

        $registerCount = is_array($this->value) ? count($this->value) : 0;
        if ($registerCount) {
            $registerCount = 0;
            if ($status = SimplerenewAddon::getList()) {
                foreach ($this->value as $id => $addon) {
                    $addon = (object)$addon;
                    $current = isset($status[$id]) ? $status[$id] : null;
                    if ($current) {
                        $registerCount++;
                        $baseName = $this->name . "[{$id}]";
                        foreach (get_object_vars($addon) as $key => $value) {
                            $name   = $baseName . '[' . $key . ']';
                            $value  = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
                            $html[] = "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\"/>";
                        }
                    }
                }
            }
        }

        if ($registerCount) {
            return join('', $html);
        }
        return '<p>' . JText::_('COM_SIMPLERENEW_ADDONS_NONE') . '</p>';
    }

    protected function getLabel()
    {
        return '';
    }
}
