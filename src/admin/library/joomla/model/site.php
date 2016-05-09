<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry;

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

        $this->state->set('parameters.component', SimplerenewComponentHelper::getParams());
    }

    /**
     * Get component params merged with menu params
     *
     * @return Registry
     */
    public function getParams()
    {
        /**
         * @var Registry $state
         * @var Registry $params
         */
        $params = clone $this->state->get('parameters.component');
        if ($menuParams = $this->state->get('parameters.menu')) {
            if ($menuParams instanceof Registry) {
                $menuParams = $menuParams->toObject();
            }
            $params->loadObject($menuParams);
        }

        return $params;
    }
}
