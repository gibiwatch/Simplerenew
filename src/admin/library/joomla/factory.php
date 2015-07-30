<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Cms\Joomla\Services\Simplerenew;
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
        $config = array();
        if (empty(static::$SimplerenewContainers[$key])) {
            // convert Joomla config parameters into Simplerenew configuration options
            if ($gateway = $params->get('gateway')) {
                $billingRequired = explode(',', $params->get('basic.billingAddress'));

                $config = array(
                    'gateway'      => SimplerenewUtilitiesArray::fromObject($gateway),
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
            }

            // Allow devs to create additional customizations for a site
            $settingsPath = SIMPLERENEW_LIBRARY . '/simplerenew/.settings.json';
            if ($settings = is_file($settingsPath) ? file_get_contents($settingsPath) : array()) {
                $config = array_merge(json_decode($settings, true), $config);
            }

            $container = new Container();
            $services  = new Simplerenew($config);
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
