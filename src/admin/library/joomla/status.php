<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Various indicators of installation status
 *
 * Class SimplerenewStatus
 *
 * @property bool   $configured      Gateway configuration has been at least visited and saved
 * @property bool   $gateway         Gateway configuration is valid and working
 * @property int    $plans           Number of plans that have been created
 * @property int    $subscribeViews  Number of subscribe forms that have been created on the front end
 * @property object $update          Information about update if one is available
 */
class SimplerenewStatus
{
    /**
     * @var bool
     */
    protected $configured = false;

    /**
     * @var bool
     */
    protected $gateway = false;

    /**
     * @var int
     */
    protected $plans = 0;

    /**
     * @var object
     */
    protected $update = null;

    /**
     * @var int
     */
    protected $subscribeViews = 0;

    public function __construct()
    {
        $this->configured = (bool)SimplerenewComponentHelper::getParams()->get('gateways');

        if ($this->configured) {
            try {
                $this->gateway = SimplerenewFactory::getContainer()->getAccount()->validConfiguration();

            } catch (Exception $e) {
                $this->gateway = false;
            }
        }

        // Count of existing plans
        $this->plans = SimplerenewFactory::getDbo()
            ->setQuery('Select count(*) From #__simplerenew_plans')
            ->loadResult();

        // Count of subscribe views
        $site  = SimplerenewHelper::getApplication('site');
        $menus = $site->getMenu()->getItems('component', 'com_simplerenew');
        foreach ($menus as $menu) {
            if (!empty($menu->query['view']) && $menu->query['view'] == 'subscribe') {
                $this->subscribeViews++;
            }
        }

        $this->update = SimplerenewModel::getInstance('Update')->getUpdate();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }
}
