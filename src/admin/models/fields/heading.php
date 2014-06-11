<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class JFormFieldHeading extends JFormField
{
    public function getInput()
    {
        return '' ;
    }

    public function getLabel()
    {
        $tag = $this->element['tag'] ? (string)$this->element['tag'] : 'p';

        return '<' . $tag
            . ' style="clear: both; padding: 10px 0px; margin: 0;">'
            . $this->element['label']
            . '</' . $tag . '>';
    }
}
