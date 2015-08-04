<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewAddon
{
    /**
     * Register an extension as an addon. Primarily to be used by addons during
     * installation, but can be used at any time.
     *
     * @param string          $title
     * @param JTableExtension $addon
     * @param string          $initPath
     *
     * @return bool
     */
    public static function register($title, JTableExtension $addon, $initPath = null)
    {
        $simplerenew = SimplerenewHelper::getExtensionTable();

        if ($addon->type == 'component' && !$initPath) {
            $path = '/administrator/components/' . $addon->element . '/include.php';
            if (is_file(JPATH_ROOT . $path)) {
                $initPath = $path;
            }
        }

        $addons = $simplerenew->params->get('addons', array());

        $addons[$addon->extension_id] = array(
            'title' => $title,
            'init'  => $initPath
        );

        $simplerenew->params->set('addons', $addons);

        $simplerenew->params = $simplerenew->params->toString();
        return $simplerenew->store();
    }

    /**
     * Run initialise routines for all registered addons
     *
     * @return void
     */
    public static function load()
    {
        $addons    = SimplerenewComponentHelper::getParams()->get('addons', array());
        $extension = JTable::getInstance('Extension');

        foreach ($addons as $id => $addon) {
            if (!empty($addon->init) && is_file(JPATH_ROOT . $addon->init)) {
                $extension->load($id);
                if ($extension->enabled) {
                    require_once JPATH_ROOT . $addon->init;
                }
            }
        }
    }

    /**
     * Get full extension information on all registered addons
     *
     * @return array Array of JTableExtension objects
     */
    public static function getList()
    {
        if ($registered = (array)SimplerenewComponentHelper::getParams()->get('addons', array())) {
            $ids = array_keys($registered);

            $db    = SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('extension_id in (' . join(',', $ids) . ')');

            if ($list = $db->setQuery($query)->loadObjectList('extension_id')) {
                foreach ($list as $id => $addon) {
                    $table = JTable::getInstance('Extension');
                    $table->setProperties($addon);
                    $list[$id] = $table;
                }
            }

            return $list;
        }

        return array();
    }
}
