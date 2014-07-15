<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('Usergrouplist');

/**
 * This class is incompatible with Joomla 2.x
 * 
 * Class JFormFieldFilteredgroup
 */
class JFormFieldFilteredgroup extends JFormFieldUserGroupList
{
    protected function getOptions()
    {

        $options = parent::getOptions();

        if ($exclude = explode(',', (string)$this->element['exclude'])) {
            $filtered = array();
            foreach ($options as $option) {
                foreach ($exclude as $action) {
                    if (JAccess::checkGroup($option->value, $action)) {
                        break 2;
                    }
                }
                $filtered[] = $option;
            }
        }

        return $filtered;
    }
}
