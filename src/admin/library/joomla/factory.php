<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Configuration;
use Simplerenew\Services;
use Simplerenew\Container;

defined('_JEXEC') or die();

abstract class SimplerenewFactory extends JFactory
{
    /**
     * @var array
     */
    protected static $SimplerenewContainers = array();

    /**
     * @var SimplerenewStatus
     */
    protected static $SimplerenewStatus = null;

    /**
     * Get a Simplerenew container class for the designated gateway
     *
     * @param string $gateway
     *
     * @return Container
     * @throws Exception
     */
    public static function getContainer($gateway = null)
    {
        $params  = SimplerenewComponentHelper::getParams();
        $gateway = $gateway ?: 'Recurly';

        $config = array();
        if (empty(static::$SimplerenewContainers[$gateway])) {
            // convert Joomla config parameters into Simplerenew configuration options
            $gatewayConfig   = $params->get('gateway.' . strtolower($gateway));
            $billingRequired = explode(',', $params->get('basic.billingAddress'));

            $config = array(
                'gateway'      => SimplerenewUtilitiesArray::fromObject($gatewayConfig),
                'billing'      => array(
                    'required' => array_filter(array_map('trim', $billingRequired))
                ),
                'subscription' => array(
                    'allowMultiple' => $params->get('basic.allowMultiple')
                ),
                'user'         => array(
                    'group' => array(
                        'default'    => (int)$params->get('basic.defaultGroup'),
                        'expiration' => (int)$params->get('basic.expirationGroup')
                    )
                )
            );

            // Allow devs to create additional customizations for a site
            $settingsPath = SIMPLERENEW_LIBRARY . '/simplerenew/.settings.json';
            if ($settings = is_file($settingsPath) ? file_get_contents($settingsPath) : array()) {
                $config = array_merge(json_decode($settings, true), $config);
            }

            $container = new Container(
                array(
                    'cmsNamespace'  => 'Simplerenew\Cms\Joomla',
                    'gateway'       => $gateway,
                    'configuration' => new Configuration($config)
                )
            );
            $container->register(new Services());

            static::$SimplerenewContainers[$gateway] = $container;
        }
        return static::$SimplerenewContainers[$gateway];
    }

    /**
     * Get containers for all known/installed gateways
     *
     * @TODO: This isn't well done. Need a better means of finding installed gateways
     *
     * @return array
     */
    public static function getAllGatewayContainers()
    {
        $primary  = static::getContainer();
        $gateways = SimplerenewFactory::getContainer()->events->trigger('simplerenewLoadGateway');

        $containers = array($primary);
        foreach ($gateways as $gateway) {
            if ($primary->gateway != $gateway) {
                $containers[] = static::getContainer($gateway);
            }
        }

        return $containers;
    }

    /**
     * Status information about Simplerenew
     *
     * @return SimplerenewStatus
     */
    public static function getStatus()
    {
        if (static::$SimplerenewStatus === null) {
            static::$SimplerenewStatus = new SimplerenewStatus();
        }
        return static::$SimplerenewStatus;
    }
}
