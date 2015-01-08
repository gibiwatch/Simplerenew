<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

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
        $params = $params ? : SimplerenewComponentHelper::getParams();
        $key    = sha1($params->toString());

        if (empty(static::$SimplerenewContainers[$key])) {
            $config = array(
                'user'    => array(
                    'adapter'         => 'joomla',
                    'defaultGroup'    => $params->get('basic.defaultGroup'),
                    'expirationGroup' => $params->get('basic.expirationGroup')
                ),
                'account' => array(
                    'billingAddress' => $params->get('basic.billingAddress')
                ),
                'gateway' => array(
                    'recurly' => (array)$params->get('gateway.recurly')
                )
            );

            static::$SimplerenewContainers[$key] = new Container($config);
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
