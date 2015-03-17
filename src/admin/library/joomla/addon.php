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

        if ($addon->type == 'component') {
            $initPath = $initPath ?: JPATH_ADMINISTRATOR . '/components/' . $addon->element . '/include.php';
        }

        $addons = $simplerenew->params->get('addons', array());

        // Prevent duplication of already registered addons
        $idx = call_user_func(
            function ($id, array $addons) {
                foreach ($addons as $i => $addon) {
                    if ($id == $addon->extension_id) {
                        return $i;
                    }
                }
                return count($addons);
            },
            $addon->extension_id,
            $addons
        );

        $addons[$idx] = array(
            'title'        => $title,
            'extension_id' => $addon->extension_id,
            'init'         => $initPath
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

        foreach ($addons as $addon) {
            if (!empty($addon->init) && is_file($addon->init)) {
                $extension->load($addon->extension_id);
                if ($extension->enabled) {
                    require_once $addon->init;
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
        if ($registered = SimplerenewComponentHelper::getParams()->get('addons', array())) {
            $ids = array();
            foreach ($registered as $addon) {
                $ids[] = $addon->extension_id;
            }

            $db    = SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('extension_id in (' . join(',', $ids) . ')');

            if ($list = $db->setQuery($query)->loadObjectList('extension_id')) {
                foreach ($list as $idx => $addon) {
                    $table = JTable::getInstance('Extension');
                    $table->setProperties($addon);
                    $list[$idx] = $table;
                }
            }

            return $list;
        }

        return array();
    }
}
