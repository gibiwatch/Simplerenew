<?php
/**
 * @package    Simplerenew
 * @subpackage
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('text');

class JFormFieldPlancode extends JFormFieldText
{
    public function getInput()
    {
        $id = $this->form->getField('id');
        if (!$this->value || ($id && !$id->value)) {
            return parent::getInput();
        }

        $html = array(
            '<input',
            'type="hidden"',
            'name="' . $this->name . '"',
            'id="' . $this->id . '"',
            'value="' . $this->value . '"',
            'readonly'
        );

        return join(' ', $html) . '/>' . $this->value;
    }
}
