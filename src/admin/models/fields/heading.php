<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class JFormFieldHeading extends JFormField
{
    public function getInput()
    {
        return '';
    }

    public function getLabel()
    {
        $tag  = $this->element['tag'] ? (string)$this->element['tag'] : 'p';
        $text = JText::_((string)$this->element['label']);

        $attribs = array();
        if ($this->element['class']) {
            $attribs['class'] = (string)$this->element['class'];
        }

        $html = array('<' . $tag);
        if ($attribs) {
            $html[] = SimplerenewUtilitiesArray::toString($attribs);
        }
        $html[] = 'style="clear: both; padding: 10px 0px; margin: 0;">';
        $html[] = $text;
        $html[] = '</' . $tag . '>';

        return join(' ', $html);
    }
}
