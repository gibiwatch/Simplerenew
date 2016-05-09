<?php
/**
 * @package    Simplerenew
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_installer/models/update.php';

class SimplerenewModelUpdate extends InstallerModelUpdate
{
    /**
     * We need to make sure the installer language strings are loaded
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        JFactory::getLanguage()->load('com_installer');

    }

    /**
     * Override to load only Simple Renew update status record
     *
     * @param null $ordering
     * @param null $direction
     *
     * @return void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);

        $eid = SimplerenewComponentHelper::getComponent()->id;
        $this->setState('filter.extension_id', $eid);
    }

    /**
     * Get the single record regarding Simple Renew update status
     *
     * @return null|object
     */
    public function getUpdate()
    {
        $items = $this->getItems();
        if (is_array($items) && count($items) == 1) {
            return $items[0];
        }

        return null;
    }
}
