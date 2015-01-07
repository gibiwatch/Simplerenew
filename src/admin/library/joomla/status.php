<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * Various indicators of installation status
 *
 * Class SimplerenewStatus
 *
 * @property bool $configured Gateway configuration has been at least visited and saved
 * @property bool $gateway    Gateway configuration is valid and working
 * @property int  $plans      Number of plans that have been created
 * @property int  $subscribe  Number of subscribe forms that have been created on the front end
 */
class SimplerenewStatus
{
    /**
     * @var bool
     */
    protected $configured = null;

    /**
     * @var bool
     */
    protected $gateway = null;

    /**
     * @var int
     */
    protected $plans = 0;

    /**
     * @var int
     */
    protected $subscribe = 0;

    public function __construct()
    {
        $this->configured = (bool)SimplerenewComponentHelper::getParams()->get('gateway');

        $this->gateway = $this->configured;
        if ($this->gateway) {
            $this->gateway = SimplerenewFactory::getContainer()->getAccount()->validConfiguration();
        }

        $this->plans = SimplerenewFactory::getDbo()
            ->setQuery('Select count(*) From #__simplerenew_plans')
            ->loadResult();

        // Find all instances of the subscribe view

        $site  = SimplerenewHelper::getApplication('site');
        $menus = $site->getMenu()->getItems('component', 'com_simplerenew');
        foreach ($menus as $menu) {
            if (!empty($menu->query['view']) && $menu->query['view'] == 'subscribe') {
                $this->subscribe++;
            }
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }
}
