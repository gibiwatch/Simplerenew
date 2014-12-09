<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Uri\Uri;
use Simplerenew\Container;

defined('_JEXEC') or die();

abstract class SimplerenewFactory extends JFactory
{
    /**
     * @var array
     */
    protected static $SimplerenewContainers = array();

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

        if (empty(self::$SimplerenewContainers[$key])) {
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

            self::$SimplerenewContainers[$key] = new Container($config);
        }
        return self::$SimplerenewContainers[$key];
    }

    /**
     * @param string $uri
     *
     * @return Uri|JUri
     */
    public static function getURI($uri = 'SERVER')
    {
        if (class_exists('Joomla\\Uri\\Uri')) {
            return new Uri($uri);
        }

        return JUri::getInstance($uri);
    }
}
