<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\Installer\AbstractScript;

defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library';
if (!is_dir($includePath)) {
    $includePath = __DIR__ . '/library';
}

if (file_exists($includePath . '/Installer/include.php')) {
    require_once $includePath . '/Installer/include.php';
} else {
    throw new Exception('[Simplerenew] Alledia Installer not found');
}

class com_simplerenewInstallerScript extends AbstractScript
{
    /**
     * @var string The minimum previous version for updates
     */
    protected $minimumVersion = '1.1.1';

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
     * @var string
     */
    protected $previousVersion = '0.0.0';

    /**
     * @param string                     $type
     * @param JInstallerAdapterComponent $parent
     *
     * @return bool
     */
    public function preFlight($type, $parent)
    {
        $success = parent::preFlight($type, $parent);
        if ($success && $type == 'update') {
            if (version_compare($this->previousVersion, $this->minimumVersion, 'lt')) {
                JFactory::getApplication()->enqueueMessage(
                    JText::sprintf('COM_SIMPLERENEW_ERROR_INSTALL_MINVERSION', $this->minimumVersion),
                    'error'
                );
                return false;
            }
        }

        return $success;
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
        $this->fixMenus();

        $this->showMessages();

        // Show additional installation messages
        $file = strpos($type, 'install') === false ? $type : 'install';
        $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/views/welcome/tmpl/' . $file . '.php';
        if (file_exists($path)) {
            require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
            JFactory::getLanguage()->load('com_simplerenew', JPATH_ADMINISTRATOR . '/components/com_simplerenew');
            require_once $path;
        }
    }

    /**
     * Install related extensions
     * Overriding the Alledia Install because we're specifying differently
     *
     * @return void
     */
    protected function installRelated()
    {
        parent::installRelated();

        if ($this->relatedExtensions) {
            $source = $this->installer->getPath('source');

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

                            $typeName  = trim(($folder ?: '') . ' ' . $type);
                            $text      = 'LIB_ALLEDIAINSTALLER_RELATED_' . ($isNew ? 'INSTALL' : 'UPDATE');
                            $installer = new JInstaller();
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
        parent::uninstallRelated();

        if ($this->relatedExtensions) {
            $installer = new JInstaller();

            foreach ($this->relatedExtensions as $type => $folders) {
                foreach ($folders as $folder => $extensions) {
                    foreach ($extensions as $element => $settings) {
                        if ($settings[1]) {
                            if ($current = $this->findExtension($type, $element, $folder)) {
                                $msg     = 'LIB_ALLEDIAINSTALLER_RELATED_UNINSTALL';
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

    /**
     * On new install, this will check and fix any menus that may have been created
     * in a previous installation.
     *
     * @return void
     */
    protected function fixMenus()
    {
        $db          = JFactory::getDbo();
        $componentId = JComponentHelper::getComponent('com_simplerenew')->id;

        $query = $db->getQuery(true)
            ->update('#__menu')
            ->set('component_id = ' . $db->quote($componentId))
            ->where(
                array(
                    'type = ' . $db->quote('component'),
                    'link LIKE ' . $db->quote('%option=com_simplerenew%')
                )
            );
        $db->setQuery($query)->execute();
    }
}
