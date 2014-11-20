<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class com_simplerenewInstallerScript
{
    /**
     * @var array Obsolete folders/files to be deleted
     * Start with [/admin|/site|/media] for location
     */
    protected $obsoleteItems = array(
        '/admin/library/configuration.json',
        '/media/js/tabs.js',
        '/admin/library/simplerenew/Notify/Handler/None.php',
        '/site/views/change',
        '/admin/views/dashboard',
        '/admin/controllers/test.php',
        '/site/views/renewal/tmpl/multiple.php',
        '/site/views/renewal/tmpl/reactivate.php',
        '/site/views/renewal/tmpl/cancel.php'
    );

    /**
     * @var array Related extensions required or useful with the component
     *            type => [ (folder) => [ (element) => [ (publish), (uninstall), (ordering) ] ] ]
     */
    protected $relatedExtensions = array(
        'plugin' => array(
            'system' => array(
                'simplerenew' => array(1, 1, null)
            ),
            'user'   => array(
                'simplerenew' => array(1, 1, null)
            )
        )
    );

    /**
     * @var JInstaller
     */
    protected $installer = null;

    /**
     * @var SimpleXMLElement
     */
    protected $manifest = null;

    /**
     * @var string
     */
    protected $previousVersion = '0.0.0';

    /**
     * @var string
     */
    protected $mediaFolder = null;

    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @param JInstallerAdapterComponent $parent
     *
     * @return void
     */
    public function initprops($parent)
    {
        $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/simplerenew.xml';
        if (is_file($path)) {
            $previousManifest      = JInstaller::parseXMLInstallFile($path);
            $this->previousVersion = $previousManifest['version'];
        }

        $this->installer = $parent->get('parent');
        $this->manifest  = $this->installer->getManifest();
        $this->messages  = array();

        if ($media = $this->manifest->media) {
            $path              = JPATH_SITE . '/' . $media['folder'] . '/' . $media['destination'];
            $this->mediaFolder = $path;
        }
    }

    /**
     * @param JInstallerAdapterComponent $parent
     *
     * @return bool
     */
    public function install($parent)
    {
        return true;
    }

    /**
     * @param JInstallerAdapterComponent $parent
     *
     * @return bool
     */
    public function discover_install($parent)
    {
        return $this->install($parent);
    }

    /**
     * @param JInstallerAdapterComponent $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
        $this->initprops($parent);
        $this->uninstallRelated();
        $this->showMessages();
    }

    /**
     * @param JInstallerAdapterComponent $parent
     *
     * @return bool
     */
    public function update($parent)
    {
        return true;
    }

    /**
     * @param string                     $type
     * @param JInstallerAdapterComponent $parent
     *
     * @return bool
     */
    public function preFlight($type, $parent)
    {
        $this->initprops($parent);

        if ($type == 'update') {
            if (version_compare($this->previousVersion, '0.1.0', 'lt')) {
                JFactory::getApplication()->enqueueMessage(
                    'Please update to at least v0.1.0 (First Beta) before updating to this version',
                    'error'
                );
                return false;
            }

            // ** Fix issue with typo in schema updates **
            $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/sql/updates/mysql/0.045.sql';
            if (is_file($path)) {
                unlink($path);
            }
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('s.*')
                ->from('#__schemas s')
                ->innerJoin('#__extensions e ON e.extension_id = s.extension_id')
                ->where(
                    array(
                        'e.element = ' . $db->quote('com_simplerenew'),
                        's.version_id = ' . $db->quote('0.045')
                    )
                );

            if ($schema = $db->setQuery($query)->loadObject()) {
                $schema->version_id = '0.1.0';
                $db->updateObject('#__schemas', $schema, 'extension_id');
            }
            // ** End of temporary schema fix **

            $this->clearUpdateServers();
        }

        return true;
    }

    /**
     * @param string                     $type
     * @param JInstallerAdapterComponent $parent
     *
     * @return void
     */
    public function postFlight($type, $parent)
    {
        $this->setDefaultParams($type);
        $this->installRelated();
        $this->clearObsolete();
        $this->checkDB();

        $this->showMessages();

        // Show additional installation messages
        $file = strpos($type, 'install') === false ? $type : 'install';
        $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/views/welcome/tmpl/' . $file . '.php';
        if (file_exists($path)) {
            JFactory::getLanguage()->load('com_simplerenew', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
            require_once $path;
        }
    }

    /**
     * Install related extensions
     *
     * @return void
     */
    protected function installRelated()
    {
        if ($this->relatedExtensions) {
            $installer = new JInstaller();
            $source    = $this->installer->getPath('source');

            foreach ($this->relatedExtensions as $type => $folders) {
                foreach ($folders as $folder => $extensions) {
                    foreach ($extensions as $element => $settings) {
                        $path = $source . '/' . $type;
                        if ($type == 'plugin') {
                            $path .= '/' . $folder;
                        }
                        $path .= '/' . $element;
                        if (is_dir($path)) {
                            $current = $this->findExtension($type, $element, $folder);
                            $isNew   = empty($current);

                            $typeName = trim(($folder ?: '') . ' ' . $type);
                            $text     = 'COM_SIMPLERENEW_RELATED_' . ($isNew ? 'INSTALL' : 'UPDATE');
                            if ($installer->install($path)) {
                                $this->setMessage(JText::sprintf($text, $typeName, $element));
                                if ($isNew) {
                                    $current = $this->findExtension($type, $element, $folder);
                                    if ($settings[0]) {
                                        $current->publish();
                                    }
                                    if ($settings[2] && ($type == 'plugin')) {
                                        $this->setPluginOrder($current, $settings[2]);
                                    }
                                }
                            } else {
                                $this->setMessage(JText::sprintf($text . '_FAIL', $typeName, $element), 'error');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Uninstall the related extensions that are useless without the component
     */
    protected function uninstallRelated()
    {
        if ($this->relatedExtensions) {
            $installer = new JInstaller();

            foreach ($this->relatedExtensions as $type => $folders) {
                foreach ($folders as $folder => $extensions) {
                    foreach ($extensions as $element => $settings) {
                        if ($settings[1]) {
                            if ($current = $this->findExtension($type, $element, $folder)) {
                                $msg     = 'COM_SIMPLERENEW_RELATED_UNINSTALL';
                                $msgtype = 'message';
                                if (!$installer->uninstall($current->type, $current->extension_id)) {
                                    $msg .= '_FAIL';
                                    $msgtype = 'error';
                                }
                                $this->setMessage(JText::sprintf($msg, $type, $element), $msgtype);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $type
     * @param string $element
     * @param string $folder
     *
     * @return JTable
     */
    protected function findExtension($type, $element, $folder = null)
    {
        $row = JTable::getInstance('extension');

        $terms = array(
            'type'    => $type,
            'element' => ($type == 'module' ? 'mod_' : '') . $element
        );
        if ($type == 'plugin') {
            $terms['folder'] = $folder;
        }
        $eid = $row->find($terms);
        if ($eid) {
            $row->load($eid);
            return $row;
        }
        return null;
    }

    /**
     * Set requested ordering for selected plugin extension
     * Accepted ordering arguments:
     * (n<=1 | first) First within folder
     * (* | last) Last within folder
     * (before:element) Before the named plugin
     * (after:element) After the named plugin
     *
     * @param JTable $extension
     * @param string $order
     *
     * @return void
     */
    protected function setPluginOrder(JTable $extension, $order)
    {
        if ($extension->type == 'plugin' && !empty($order)) {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('extension_id, element');
            $query->from('#__extensions');
            $query->where(
                array(
                    $db->qn('folder') . ' = ' . $db->q($extension->folder),
                    $db->qn('type') . ' = ' . $db->q($extension->type)
                )
            );
            $query->order($db->qn('ordering'));

            $plugins = $db->setQuery($query)->loadObjectList('element');

            // Set the order only if plugin already successfully installed
            if (array_key_exists($extension->element, $plugins)) {
                $target = array(
                    $extension->element => $plugins[$extension->element]
                );
                $others = array_diff_key($plugins, $target);

                if ((is_numeric($order) && $order <= 1) || $order == 'first') {
                    // First in order
                    $neworder = array_merge($target, $others);
                } elseif (($order == '*') || ($order == 'last')) {
                    // Last in order
                    $neworder = array_merge($others, $target);
                } elseif (preg_match('/^(before|after):(\S+)$/', $order, $match)) {
                    // place before or after named plugin
                    $place    = $match[1];
                    $element  = $match[2];
                    $neworder = array();
                    $previous = '';

                    foreach ($others as $plugin) {
                        if ((($place == 'before') && ($plugin->element == $element)) || (($place == 'after') && ($previous == $element))) {
                            $neworder = array_merge($neworder, $target);
                        }
                        $neworder[$plugin->element] = $plugin;
                        $previous                   = $plugin->element;
                    }
                    if (count($neworder) < count($plugins)) {
                        // Make it last if the requested plugin isn't installed
                        $neworder = array_merge($neworder, $target);
                    }
                } else {
                    $neworder = array();
                }

                if (count($neworder) == count($plugins)) {
                    // Only reorder if have a validated new order
                    JModelLegacy::addIncludePath(
                        JPATH_ADMINISTRATOR . '/components/com_plugins/models',
                        'PluginsModels'
                    );
                    $model = JModelLegacy::getInstance('Plugin', 'PluginsModel');

                    $ids = array();
                    foreach ($neworder as $plugin) {
                        $ids[] = $plugin->extension_id;
                    }
                    $order = range(1, count($ids));
                    $model->saveorder($ids, $order);
                }
            }
        }
    }

    /**
     * Display messages from array
     *
     * @return void
     */
    protected function showMessages()
    {
        $app = JFactory::getApplication();
        foreach ($this->messages as $msg) {
            $app->enqueueMessage($msg[0], $msg[1]);
        }
    }

    /**
     * Add a message to the message list
     *
     * @param string $msg
     * @param string $type
     *
     * @return void
     */
    protected function setMessage($msg, $type = 'message')
    {
        $this->messages[] = array($msg, $type);
    }

    /**
     * Delete obsolete files and folders
     */
    protected function clearObsolete()
    {
        if ($this->obsoleteItems) {
            $admin = $this->installer->getPath('extension_administrator');
            $site  = $this->installer->getPath('extension_site');

            $search  = array('#^/admin#', '#^/site#');
            $replace = array($admin, $site);
            if ($this->mediaFolder) {
                $search[]  = '#^/media#';
                $replace[] = $this->mediaFolder;
            }

            foreach ($this->obsoleteItems as $item) {
                $path = preg_replace($search, $replace, $item);
                if (is_file($path)) {
                    $success = JFile::delete($path);
                } elseif (is_dir($path)) {
                    $success = JFolder::delete($path);
                } else {
                    $success = null;
                }
                if ($success !== null) {
                    $this->setMessage('Delete ' . $path . ($success ? ' [OK]' : ' [FAILED]'));
                }
            }
        }
    }

    /**
     * @param string $type
     *
     * @return void
     */
    protected function setDefaultParams($type)
    {
        /** @var JTableExtension $table */
        $table = JTable::getInstance('Extension');
        $table->load(array('element' => 'com_simplerenew'));

        $params = new JRegistry($table->params);

        $setParams    = ($type == 'install');
        $defaultGroup = JComponentHelper::getParams('com_users')->get('new_usertype');

        if ($type == 'update') {
            // v0.0.47: Clear up old setting that might be invalid
            if ($params->get('basic.defaultGroup') == $defaultGroup) {
                $params->set('basic.defaultGroup', '');
                $setParams = true;
            }

            $paramArray = $params->toArray();
            if (!isset($paramArray['basic']['billingAddress'])) {
                $params->set('basic.billingAddress', $params->get('account.billingAddress'));
                $setParams = true;
            }
        }

        // On initial installation, set some defaults
        if ($setParams) {
            // Set defaults based on config.xml construction
            $path = $this->installer->getPath('source') . '/admin/config.xml';
            if (file_exists($path)) {
                $config      = new SimpleXMLElement($path, 0, true);
                $defaultFont = 'none';

                // Look for the first fontFamily entry that isn't 'none'
                $fonts = $config->xpath("//field[@name='fontFamily']/option");
                foreach ($fonts as $font) {
                    if ($font['value'] != 'none') {
                        $defaultFont = (string)$font['value'];
                        break;
                    }
                }
                $params->set('themes.fontFamily', $defaultFont);
            }
        }

        // Must have the expiration plan group set
        if ($params->get('basic.expirationGroup') == '') {
            $params->set('basic.expirationGroup', $defaultGroup);
            $setParams = true;
        }

        // Must have default payment option set
        if (!$params->get('basic.paymentOptions', array())) {
            $params->set('basic.paymentOptions', array('cc'));
            $setParams = true;
        }

        if ($setParams) {
            $table->params = $params->toString();
            $table->store();
        }
    }

    /**
     * Use this in preflight to clear out obsolete update servers when the url has changed.
     */
    protected function clearUpdateServers()
    {
        $sr = JComponentHelper::getComponent('com_simplerenew');

        $db = JFactory::getDbo();
        $db->setQuery('Select update_site_id From #__update_sites_extensions where extension_id=' . (int)$sr->id);
        if ($list = $db->loadColumn()) {
            $db->setQuery('Delete From #__update_sites_extensions where extension_id=' . (int)$sr->id);
            $db->execute();

            $db->setQuery('Delete From #__update_sites Where update_site_id IN (' . join(',', $list) . ')');
            $db->execute();
        }
    }

    /**
     * Check DB structure/entries
     *
     * @return void
     */
    protected function checkDB()
    {
        $db = JFactory::getDbo();

        $path = $this->installer->getPath('extension_administrator') . '/sql';

        $countries = $db->setQuery('Select count(*) From #__simplerenew_countries')->loadResult();
        $file      = $path . '/iso3166-2.json';
        if ($countries == 0 && file_exists($file)) {
            $countries = json_decode(file_get_contents($file));
            foreach ($countries as $country) {
                $db->insertObject('#__simplerenew_countries', $country);
            }
        }

        $regions = $db->setQuery('Select count(*) From #__simplerenew_regions')->loadResult();
        $file    = $path . '/regions.json';
        if ($regions == 0 && file_exists($file)) {
            $regions = json_decode(file_get_contents($file));
            foreach ($regions as $region) {
                $db->insertObject('#__simplerenew_regions', $region);
            }
        }
    }
}
