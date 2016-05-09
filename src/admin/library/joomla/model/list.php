<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class SimplerenewModelList extends JModelList
{
    /**
     * Provided for ease of use in Joomla 2.5 where it has no use
     *
     * @param array $data
     * @param bool  $loadData
     *
     * @return JForm|null
     */
    public function getFilterForm($data = array(), $loadData = true)
    {
        if (version_compare(JVERSION, '3', 'lt')) {
            return null;
        }
        return parent::getFilterForm($data, $loadData);
    }

    /**
     * Provided for ease of use in Joomla 2.5 where it has no use
     *
     * @return array
     */
    public function getActiveFilters()
    {
        if (version_compare(JVERSION, '3', 'lt')) {
            return array();
        }
        return parent::getActiveFilters();
    }
}
