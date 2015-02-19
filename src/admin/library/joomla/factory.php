<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Configuration;
use Simplerenew\Container;
use Simplerenew\User\Adapter\Joomla;

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
     * Get the Simplerenew container class
     *
     * @TODO: Review Factory/DI pattern for possible improvement
     *
     * @param JRegistry $params
     *
     * @return Container
     * @throws Exception
     */
    public static function getContainer(JRegistry $params = null)
    {
        $params = $params ?: SimplerenewComponentHelper::getParams();
        $key    = sha1($params->toString());
        if (empty(static::$SimplerenewContainers[$key])) {
            // convert Joomla config parameters into Simplerenew configuration options
            $recurly         = $params->get('gateway.recurly');
            $billingRequired = explode(',', $params->get('basic.billingAddress'));

            $config = array(
                'gateway' => array(
                    'mode' => $recurly->mode,
                    'live' => array(
                        'apiKey'    => $recurly->liveApikey,
                        'publicKey' => $recurly->livePublickey
                    ),
                    'test' => array(
                        'apiKey'    => $recurly->testApikey,
                        'publicKey' => $recurly->testPublickey
                    )
                ),
                'billing' => array(
                    'required' => array_filter(array_map('trim', $billingRequired))
                ),
                'user'    => array(
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

            $container = new Container();
            $services  = new Simplerenew\Cms\Joomla\Services\Simplerenew($config);
            $container->register($services);

            static::$SimplerenewContainers[$key] = $container;
        }
        return static::$SimplerenewContainers[$key];
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
