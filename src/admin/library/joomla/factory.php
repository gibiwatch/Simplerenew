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
     * @var Container
     */
    protected static $SimplerenewContainer = null;

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
        if (!self::$SimplerenewContainer instanceof Container) {
            $params = $params ? : SimplerenewComponentHelper::getParams();

            $config = array(
                'user'    => array(
                    'adapter' => 'joomla'
                ),
                'gateway' => array(
                    'recurly' => (array)$params->get('gateway.recurly')
                ),
                'account' => array(
                    'codeMask' => $params->get('account.codeMask', '%s')
                )
            );

            self::$SimplerenewContainer = new Container($config);
        }
        return self::$SimplerenewContainer;
    }
}
