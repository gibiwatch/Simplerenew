<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
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
     * @param JInstallerAdapterComponent $parent
     *
     * @return void
     */
    public function initProperties($parent)
    {
        parent::initProperties($parent);

        $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/simplerenew.xml';
        if (is_file($path)) {
            $previousManifest      = JInstaller::parseXMLInstallFile($path);
            $this->previousVersion = $previousManifest['version'];
        }
    }

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
            if (version_compare($this->previousVersion, '0.1.0', 'lt')) {
                JFactory::getApplication()->enqueueMessage(
                    'Please update to at least v0.1.0 (First Beta) before updating to this version',
                    'error'
                );
                return false;
            }

            // ** Fix issue with typo in schema updates **
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
     * Overriding the Alledia Install because we're specifying differently
     *
     * @return void
     */
    protected function installRelated()
    {
        parent::installRelated();

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
        parent::uninstallRelated();

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
