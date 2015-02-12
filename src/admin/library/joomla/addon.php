<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
     */
    public static function load()
    {
        $addons = SimplerenewComponentHelper::getParams()->get('addons', array());

        foreach ($addons as $addon) {
            if (!empty($addon->init) && is_file($addon->init)) {
                require_once $addon->init;
            }
        }
    }
}
