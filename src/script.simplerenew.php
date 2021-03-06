<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\Installer\AbstractScript;
use Joomla\Registry\Registry;

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
        $this->checkDB();
        $this->fixMenus();

        parent::postFlight($type, $parent);
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

        $params = new Registry($table->params);

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

        if ($addonsOld = $params->get('addons')) {
            $addonsNew = array();

            foreach ($addonsOld as $addon) {
                if (property_exists($addon, 'init')) {
                    $setParams = true;
                    // As of v1.1.10 - make addon init paths relative
                    if (strpos($addon->init, JPATH_ROOT) === 0) {
                        $addon->init = substr($addon->init, strlen(JPATH_ROOT));
                    }
                }

                if (property_exists($addon, 'extension_id')) {
                    $setParams = true;
                    // As of v1.1.11b2 - Addon parameters are keyed on extension ID
                    $addonsNew[$addon->extension_id] = array(
                        'title' => $addon->title,
                        'init'  => $addon->init
                    );
                }
            }

            if ($addonsNew) {
                $params->set('addons', $addonsNew);
            }
        }

        // As of v1.1.11b1 - Nest Recurly params the way we always wanted to
        $recurlyOld = $params->get('gateway.recurly');
        if (is_object($recurlyOld) && property_exists($recurlyOld, 'liveApikey')) {
            $setParams  = true;
            $recurlyNew = array(
                'mode' => $recurlyOld->mode,
                'live' => array(
                    'apiKey'    => $recurlyOld->liveApikey,
                    'publicKey' => $recurlyOld->livePublickey
                ),
                'test' => array(
                    'apiKey'    => $recurlyOld->testApikey,
                    'publicKey' => $recurlyOld->testPublickey
                ),
            );

            $paramsData             = $params->toArray();
            $paramsData['gateways'] = $paramsData['gateway'];
            unset($paramsData['gateway']);
            $params = new Registry($paramsData);
            $params->set('gateways.recurly', $recurlyNew);
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
