<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class Com_SimplerenewInstallerScript
{
    /**
     * @var array Obsolete folders/files to be deleted - use admin/site/media for location
     */
    protected $obsoleteItems = array();

    /**
     * @var array Related extensions required or useful with the component
     *            type => [ (folder) => [ (element) => [ (publish), (uninstall), (unused) ] ] ]
     */
    protected $relatedExtensions = array(
        'module' => array(
            'site' => array(
                'srmyaccount' => array(1, 0, null)
            )
        ),
        'plugin' => array(
            'system' => array(
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
    protected $mediaFolder = null;

    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @param JAdapterComponent $parent
     *
     * @return void
     */
    public function initprops($parent)
    {
        $this->installer = $parent->get('parent');
        $this->manifest  = $this->installer->getManifest();
        $this->messages  = array();

        if ($media = $this->manifest->media) {
            $path              = JPATH_SITE . '/' . $media['folder'] . '/' . $media['destination'];
            $this->mediaFolder = $path;
        }
    }

    /**
     * @param JAdapterComponent $parent
     *
     * @return bool
     */
    public function install($parent)
    {
        return true;
    }

    /**
     * @param JAdapterComponent $parent
     *
     * @return bool
     */
    public function discover_install($parent)
    {
        return $this->install($parent);
    }

    /**
     * @param JAdapterComponent $parent
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
     * @param JAdapterComponent $parent
     *
     * @return bool
     */
    public function update($parent)
    {
        return true;
    }

    /**
     * @param string            $type
     * @param JAdapterComponent $parent
     *
     * @return bool
     */
    public function preFlight($type, $parent)
    {
        $this->initprops($parent);
        return true;
    }

    /**
     * @param string            $type
     * @param JAdapterComponent $parent
     *
     * @return void
     */
    public function postFlight($type, $parent)
    {
        $this->installRelated();

        if ($fof = $this->installFOF()) {
            if ($fof->installed) {
                $text = 'COM_SIMPLERENEW_FOF_' . ($fof->required ? 'INSTALL' : 'UPDATE');
                $this->setMessage(JText::sprintf($text, $fof->version, $fof->date->format('Y-m-d')));
            }
        }
        $this->showMessages();
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

                            $text = 'COM_SIMPLERENEW_RELATED_' . ($isNew ? 'INSTALL' : 'UPDATE');
                            if ($installer->install($path)) {
                                $this->setMessage(JText::sprintf($text, $type, $element));
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
                                $this->setMessage(JText::sprintf($text . '_FAIL', $type, $element), 'error');
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
     * Check if FoF is already installed and install if not
     *
     * @return  object object with performed actions summary
     */
    protected function installFOF()
    {
        if(version_compare(JVERSION, '3.2.0', 'ge')) {
            return null;
        }

        $src = $this->installer->getPath('source');

        // Load dependencies
        JLoader::import('joomla.filesystem.file');
        JLoader::import('joomla.utilities.date');
        $source = $src . '/assets/fof';

        if (!defined('JPATH_LIBRARIES')) {
            $target = JPATH_ROOT . '/libraries/fof';
        } else {
            $target = JPATH_LIBRARIES . '/fof';
        }

        $haveToInstallFOF = true;
        if (is_dir($target)) {
            $fofVersion = array();

            if (file_exists($target . '/version.txt')) {
                $rawData                 = JFile::read($target . '/version.txt');
                $info                    = explode("\n", $rawData);
                $fofVersion['installed'] = array(
                    'version' => trim($info[0]),
                    'date'    => new JDate(trim($info[1]))
                );
            } else {
                $fofVersion['installed'] = array(
                    'version' => '0.0',
                    'date'    => new JDate('2011-01-01')
                );
            }

            $rawData               = JFile::read($source . '/version.txt');
            $info                  = explode("\n", $rawData);
            $fofVersion['package'] = array(
                'version' => trim($info[0]),
                'date'    => new JDate(trim($info[1]))
            );

            $haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
        }

        $installedFOF = false;

        if ($haveToInstallFOF) {
            $versionSource = 'package';
            $installer     = new JInstaller;
            $installedFOF  = $installer->install($source);
        } else {
            $versionSource = 'installed';
        }

        if (!isset($fofVersion)) {
            $fofVersion = array();

            if (file_exists($target . '/version.txt')) {
                $rawData                 = JFile::read($target . '/version.txt');
                $info                    = explode("\n", $rawData);
                $fofVersion['installed'] = array(
                    'version' => trim($info[0]),
                    'date'    => new JDate(trim($info[1]))
                );
            } else {
                $fofVersion['installed'] = array(
                    'version' => '0.0',
                    'date'    => new JDate('2011-01-01')
                );
            }

            $rawData               = JFile::read($source . '/version.txt');
            $info                  = explode("\n", $rawData);
            $fofVersion['package'] = array(
                'version' => trim($info[0]),
                'date'    => new JDate(trim($info[1]))
            );
            $versionSource         = 'installed';
        }

        if (!($fofVersion[$versionSource]['date'] instanceof JDate)) {
            $fofVersion[$versionSource]['date'] = new JDate;
        }

        return (object)array(
            'required'  => $haveToInstallFOF,
            'installed' => $installedFOF,
            'version'   => $fofVersion[$versionSource]['version'],
            'date'      => $fofVersion[$versionSource]['date'],
        );
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
}