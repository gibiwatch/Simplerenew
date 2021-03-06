<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
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
        $gateway = ucfirst(strtolower($gateway)) ?: 'Recurly';

        if (empty(static::$SimplerenewContainers[$gateway])) {
            // convert Joomla config parameters into Simplerenew configuration options
            $gatewayConfig   = $params->get('gateways.' . strtolower($gateway));
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

            $config = new Configuration($config);

            $upgradeOrder = explode('|', (string)$params->get('basic.upgradeOrder'));
            if (!$config->get('subscription.allowMultiple') && $params->get('basic.enableUpgrade')) {
                $config->set('subscription.upgradeOrder', $upgradeOrder);
            }


            $container = new Container(
                array(
                    'cmsNamespace'  => 'Simplerenew\Cms\Joomla',
                    'gateway'       => $gateway,
                    'configuration' => $config,
                    'debug'         => $params->get('advanced.enableDebug')
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
        $gateways = SimplerenewFactory::getContainer()->events->trigger('simplerenewLoadGateway');

        foreach ($gateways as $gateway) {
            if (!isset(static::$SimplerenewContainers[$gateway])) {
                static::getContainer($gateway);
            }
        }

        return static::$SimplerenewContainers;
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
