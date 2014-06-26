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
     * @return Container
     */
    public static function getContainer()
    {
        if (!self::$SimplerenewContainer instanceof Container) {
            try {
                $params = SimplerenewComponentHelper::getParams();

                $config = array(
                    'user'    => array(
                        'adapter' => 'joomla'
                    ),
                    'gateway' => array(
                        'recurly' => (array)$params->get('gateway.recurly')
                    ),
                    'account' => array(
                        'codeMask' => $params->get('basic.codeMask', '%s')
                    )
                );

                self::$SimplerenewContainer = new Container($config);
            } catch (Exception $e) {
                $app = self::getApplication();
                if ($app->isAdmin()) {
                    $link = 'index.php?option=com_simplerenew';
                } else {
                    $link = JRoute::_('index.php');
                }
                $app->redirect(
                    $link,
                    JText::sprintf('COM_SIMPLERENEW_ERROR_CONFIGURATION', $e->getMessage()),
                    'error'
                );
            }

        }
        return self::$SimplerenewContainer;
    }
}
