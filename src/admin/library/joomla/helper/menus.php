<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Class SimplerenewHelperMenus
 *
 * This is a utility class that could be used during installation
 * when Simplerenew itself has not be fully initialized. So be sure
 * not to rely on any other Simplerenew classes.
 */
class SimplerenewHelperMenus
{
    /**
     * @var int
     */
    protected $componentId = null;

    public function __construct($component = 'com_simplerenew')
    {
        $this->componentId = JComponentHelper::getComponent($component)->id;

        $lang = JFactory::getLanguage();
        $lang->load('com_simplerenew', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
    }

    /**
     * See if any menus have been created
     *
     * @param int $menutype
     *
     * @return bool
     */
    public function exist($menutype = null)
    {
        $db = SimplerenewFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from('#__menu')
            ->where('component_id = ' . $db->quote($this->componentId));

        if ($menutype) {
            $query->where('menutype = ' . $menutype);
        }

        return (bool)($db->setQuery($query)->loadResult() > 0);
    }

    /**
     * Create menu items from XML:
     *
     * <menutype>STRING</menutype>
     * <title>STRING</title>
     * <description>STRING</description>
     * <items>
     *    <item>
     *       <title>STRING</title>
     *       <alias>STRING</alias>
     *       <link>STRING</link>
     *    </item>
     * </items>
     *
     *
     * @param SimpleXMLElement $menuDef
     * @param bool             $replace
     *
     * @return void
     * @throws Exception
     */
    public function create(SimpleXMLElement $menuDef, $replace = false)
    {
        $type      = array(
            'menutype'    => (string)$menuDef->menutype,
            'title'       => JText::_((string)$menuDef->title),
            'description' => JText::_((string)$menuDef->description)
        );
        $menuItems = $menuDef->items;

        if ($replace && $type['menutype']) {
            $this->delete($type['menutype']);
        }

        /** @var JTableMenuType $typeTable */
        $typeTable = JTable::getInstance('MenuType');
        $typeTable->load(array('menutype' => $type['menutype']));

        if (!$typeTable->id) {
            if (!$typeTable->bind($type) || !$typeTable->check() || !$typeTable->store()) {
                throw new Exception(JText::sprintf('COM_SIMPLERENEW_ERROR_MENUTYPE_CREATE', $typeTable->getError()), 500);
            }
        }

        if ($menuItems) {
            $paramBase = array(
                'menu-anchor_title'     => '',
                'menu-anchor_css'       => '',
                'menu_image'            => '',
                'menu_text'             => 1,
                'page_title'            => '',
                'show_page_heading'     => 0,
                'page_heading'          => '',
                'pageclass_sfx'         => '',
                'menu-meta_description' => '',
                'menu-meta_keywords'    => '',
                'robots'                => '',
                'secure'                => 0
            );

            /** @var JTableMenu $menuTable */
            foreach ($menuItems->children() as $menuItem) {
                $params = (string)$menuItem->params ?: '[]';
                $params = array_merge($paramBase, json_decode($params));
                $alias  = $this->stringURLSafe((string)$menuItem->alias);

                $menuTable = JTable::getInstance('menu');
                $menuTable->load(array('alias' => $alias));

                $idx = 1;
                while ($menuTable->id && $menuTable->menutype != $typeTable->menutype) {
                    // Some other menu item exists with the same alias but isn't one of ours
                    $alias = $this->stringURLSafe($menuItem->alias . '-' . $idx++);

                    $menuTable->id = null;
                    $menuTable->load(array('alias' => $alias, 'parent_id' => 1));
                }

                if ($menuTable->published == -2) {
                    // Menuitem found but in trash
                    $menuTable->delete();
                    $menuTable->id = null;
                }

                // Recreate the menuitem if it no longer exists
                if (!$menuTable->id) {
                    $data = array(
                        'menutype'     => $typeTable->menutype,
                        'title'        => JText::_($menuItem->title),
                        'alias'        => $alias,
                        'link'         => (string)$menuItem->link,
                        'type'         => 'component',
                        'published'    => 1,
                        'parent_id'    => 1,
                        'component_id' => $this->componentId,
                        'access'       => (string)$menuItem->access ?: 1,
                        'params'       => json_encode($params),
                        'home'         => 0,
                        'language'     => '*',
                        'client_id'    => 0
                    );

                    $menuTable->setLocation(1, 'last-child');
                    if (!$menuTable->bind($data) || !$menuTable->check() || !$menuTable->store()) {
                        throw new Exception(JText::sprintf('COM_SIMPLERENEW_ERROR_MENUITEM_CREATE', $menuTable->getError()));
                    }
                }
            }
        }
    }

    /**
     * Delete a menutype and all its menus
     *
     * @param $menuType
     *
     * @return void
     * @throws Exception
     */
    public function delete($menuType)
    {
        /** @var JTableMenu $table */
        $table = JTable::getInstance('MenuType');
        $table->load(array('menutype' => $menuType));
        if ($table->id) {
            $success = $table->delete();
            if (!$success) {
                throw new Exception(JText::sprintf('COM_SIMPLERENEW_ERROR_MENUTYPE_DELETE', $table->getError()), 500);
            }
        }

        /** @var JCache $cache */
        $cache = JFactory::getCache();
        $cache->clean('mod_menu');
    }

    /**
     * We need an additional wrapper for un-loaded Simplerenew
     *
     * @param $string
     *
     * @return string
     */
    protected function stringURLSafe($string)
    {
        if (class_exists('SimplerenewApplicationHelper')) {
            return SimplerenewApplicationHelper::stringURLSafe($string);
        } elseif (version_compare(JVERSION, '3.0', 'lt')) {
            return JApplication::stringURLSafe($string);
        }

        return JApplicationHelper::stringURLSafe($string);
    }
}
