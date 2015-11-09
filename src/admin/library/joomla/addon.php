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

        if ($addon->type == 'component' && !$initPath) {
            $path = '/administrator/components/' . $addon->element . '/include.php';
            if (is_file(JPATH_ROOT . $path)) {
                $initPath = $path;
            }
        }

        $addons = (array)$simplerenew->params->get('addons', array());

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
     * @return array Array of JTableExtension objects with additional properties
     */
    public static function getList()
    {
        if ($registered = SimplerenewComponentHelper::getParams()->get('addons', array())) {
            $registered = json_decode(Json_encode($registered), true);

            $ids   = array_keys($registered);
            $db    = SimplerenewFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('extension_id in (' . join(',', $ids) . ')');

            if ($list = $db->setQuery($query)->loadObjectList('extension_id')) {
                foreach ($list as $id => $addon) {
                    $addon->title = $registered[$id]['title'];
                    $addon->init  = $registered[$id]['init'];

                    $table = JTable::getInstance('Extension');

                    $table->setProperties($addon);

                    $list[$id] = $table;
                }
            }
            return $list;
        }

        return array();
    }

    /**
     * Addons can extend the configuration options in Simplerenew. Registered
     * component addons can have this done automatically by setting their
     * administration menu to hidden and providing a standard config.xml
     * file. Existing Simplerenew config fields will not be overwritten and
     * it is expected that addons will properly namespace their own options. For
     * example, to create a parameter srgroupleaders.discount and place it on
     * the addons tab:
     *
     * <config>
     *    <fieldset name="addons">
     *       <fields name="srgroupleaders">
     *          <field type="text" name="discount" label="Discount"/>
     *       </fields>
     *    </fieldset>
     * </config>
     *
     * @param JForm $form
     *
     * @return void
     */
    public static function mergeAddonConfigs(JForm $form)
    {
        // Add addon parameters only if this form supports them
        $addonFields = $form->getFieldset('addons');
        if (!$addonFields) {
            return;
        }

        $addons = static::getList();
        foreach ($addons as $id => $addon) {
            if ($addon->enabled && (!$addon->init || is_file(JPATH_ROOT . $addon->init))) {
                $statusImage = JHtml::_(
                    'image',
                    'admin/icon-16-allow.png',
                    JText::_('COM_SIMPLERENEW_ENABLED'),
                    null,
                    true
                );
            } else {
                $statusImage = JHtml::_(
                    'image',
                    'admin/icon-16-deny.png',
                    JText::_('COM_SIMPLERENEW_DISABLED'),
                    null,
                    true
                );
            }
            $statusImage = htmlspecialchars($statusImage);

            $xmlString = <<<XMLSTRING
<?xml version="1.0" encoding="utf-8"?>
<config>
    <fieldset name="addons">
        <field
            name="head_{$addon->extension_id}"
            label="{$addon->title} ({$id}) {$statusImage}"
            type="simplerenew.heading"
            tag="h4"/>
    </fieldset>
</config>
XMLSTRING;
            $form->load($xmlString, false, '/config');

            if ($addon->type == 'component') {
                $path = JPATH_ADMINISTRATOR . '/components/' . $addon->element;

                $manifestPath = $path . '/' . substr($addon->element, 4) . '.xml';
                $configPath   = $path . '/config.xml';

                if (is_file($manifestPath)) {
                    $hidden   = true;
                    $manifest = simplexml_load_file($manifestPath);
                    if ($adminMenu = $manifest->xpath('administration/menu')) {
                        $hidden = ((string)$adminMenu[0]['hidden'] == 'true'
                            || (string)$adminMenu[0]['hidden'] == 'hidden');
                    }

                    if ($hidden && is_file($configPath)) {
                        $form->loadFile($configPath, false, '/config');
                    }
                }
            }
        }
    }
}
