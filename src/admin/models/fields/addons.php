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

        if (!$this->value) {
            $html[] = '<p>' . JText::_('COM_SIMPLERENEW_ADDONS_NONE') . '</p>';

        } else {
            $html = array_merge($html, array(
                '<table cellpadding="5"><thead><tr>',
                '<th>' . JText::_('COM_SIMPLERENEW_ADDONS_ID') . '</th>',
                '<th>' . JText::_('COM_SIMPLERENEW_ADDONS_NAME') . '</th>',
                '<th>' . JText::_('COM_SIMPLERENEW_ADDONS_STATUS') . '</th>',
                '</tr></thead>',
                '<tbody>'
            ));

            $extension = JTable::getInstance('Extension');
            foreach ($this->value as $idx => $addon) {
                $extension->load($addon->extension_id);
                if ($extension->enabled) {
                    $statusImage = JHTML::image(
                        'admin/icon-16-allow.png',
                        JText::_('COM_SIMPLERENEW_ENABLED'),
                        null,
                        true
                    );
                } else {
                    $statusImage = JHTML::image(
                        'admin/icon-16-deny.png',
                        JText::_('COM_SIMPLERENEW_DISABLED'),
                        null,
                        true
                    );
                }

                $html  = array_merge($html, array(
                    '<tr>',
                    '<td style="text-align: right;">' . $addon->extension_id . '</td>',
                    '<td style="text-align: left;">' . $addon->title . '</td>',
                    '<td style="text-align: center;">' . $statusImage
                ));

                $baseName = $this->name . "[{$idx}]";
                foreach (get_object_vars($addon) as $key => $value) {
                    $name = $baseName . '[' . $key . ']';
                    $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
                    $html[] = "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\"/>";
                }

                $html[] = '</td>';
                $html[] = '</tr>';
            }

            $html = array_merge($html, array(
                '</tbody>',
                '</table>'
            ));
        }

        return join('', $html);
    }

    protected function getLabel()
    {
        return '';
    }
}
