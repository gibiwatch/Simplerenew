<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
        $state      = $this->getState();
        $params     = clone $state->get('parameters.component');
        $menuParams = $state->get('parameters.menu');

        $params->merge($menuParams);
        return $params;
    }
}
