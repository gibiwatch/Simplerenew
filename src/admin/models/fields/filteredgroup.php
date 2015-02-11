<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class JFormFieldFilteredgroup extends JFormFieldList
{
    /**
     * Cached array of the category items.
     * Copied from Joomla 3.x
     *
     * @var    array
     */
    protected static $options = array();

    protected function getOptions()
    {
        // Hash for caching
        $hash = md5($this->element->asXML());

        if (!isset(static::$options[$hash])) {
            static::$options[$hash] = parent::getOptions();

            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.id AS value')
                ->select('a.title AS text')
                ->select('COUNT(DISTINCT b.id) AS level')
                ->from('#__usergroups as a')
                ->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
                ->group('a.id, a.title, a.lft, a.rgt')
                ->order('a.lft ASC');
            $db->setQuery($query);

            if ($options = $db->loadObjectList()) {
                foreach ($options as $option) {
                    $option->text = str_repeat('- ', $option->level) . $option->text;
                }
                static::$options[$hash] = array_merge(static::$options[$hash], $options);
            }
        }

        $defaultGroup = JComponentHelper::getParams('com_users')->get('new_usertype');

        $options = static::$options[$hash];
        if ($exclude = explode(',', (string)$this->element['exclude'])) {
            $filtered = array();
            foreach ($options as $option) {
                foreach ($exclude as $action) {
                    if (JAccess::checkGroup($option->value, $action)) {
                        continue 2;
                    } elseif ($action == 'default' && $option->value == $defaultGroup) {
                        continue 2;
                    }
                }
                $filtered[] = $option;
            }
            return $filtered;
        }

        return $options;
    }
}
