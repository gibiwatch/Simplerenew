<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewModelSite extends JModelLegacy
{
    /**
     * @var string
     */
    protected $context = null;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->context = $this->option . '.' . $this->name;
    }

    public function getState($property = null, $default = null)
    {
        $init  = !$this->__state_set;
        $state = parent::getState();
        if ($init) {
            $state->set('parameters.component', SimplerenewComponentHelper::getParams());
        }
        return parent::getState($property, $default);
    }

    /**
     * Get component params merged with menu params
     *
     * @return JRegistry
     */
    public function getParams()
    {
        /**
         * @var JRegistry $state
         * @var JRegistry $params
         */
        $state      = $this->getState();
        $params     = clone $state->get('parameters.component');
        if ($menuParams = $state->get('parameters.menu')) {
            if ($menuParams instanceof JRegistry) {
                $menuParams = $menuParams->toObject();
            }
            $params->loadObject($menuParams);
        }

        return $params;
    }
}
