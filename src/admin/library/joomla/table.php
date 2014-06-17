<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewTable extends JTable
{
    /**
     * @param string $type
     * @param string $prefix
     * @param array  $config
     *
     * @return SimplerenewTable
     */
    public static function getInstance($type, $prefix = 'SimplerenewTable', $config = array())
    {
        return parent::getInstance($type, $prefix, $config);
    }

    /**
     * Automatically set create/modified dates
     *
     * @param boolean $updateNulls [optional]
     *
     * @return boolean
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate()->toSql();
        $user = JFactory::getUser();

        if (empty($this->id) && property_exists($this, 'created')) {
            if ($this->created instanceof DateTime) {
                $this->created = $this->created->format('Y-m-d H:i:s');
            } elseif (!is_string($this->created) || empty($this->created)) {
                $this->created = $date;
            }
        }

        if (empty($this->id) && !empty($user->id)
            && property_exists($this, 'created_by')
            && property_exists($this, 'created_by_alias')
        ) {
            $this->created_by = $user->id;
            $this->created_by_alias = $user->name;
        }

        if (property_exists($this, 'modified')) {
            $this->modified = $date;
            if (!empty($user->id) && property_exists($this, 'modified_by')) {
                $this->modified_by = $user->id;
            }
        }

        return parent::store($updateNulls);
    }
}
