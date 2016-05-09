<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

JFormHelper::loadFieldClass('List');

class SimplerenewFormFieldGateways extends JFormFieldList
{
    protected function getOptions()
    {
        $containers = SimplerenewFactory::getAllGatewayContainers();
        $exclude    = array_map('trim', explode(',', strtolower($this->element['exclude'])));

        $options = array();
        foreach ($containers as $gateway => $container) {
            if (!in_array(strtolower($gateway), $exclude)) {
                $options[] = JHtml::_('select.option', $gateway, $gateway);
            }
        }

        return array_merge(parent::getOptions(), $options);
    }

}
