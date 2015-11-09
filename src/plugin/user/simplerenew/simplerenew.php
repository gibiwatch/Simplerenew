<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Exception\NotFound;

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgUserSimplerenew extends JPlugin
{
    /**
     * @param object $subject
     * @param array  $config
     */
    public function __construct(&$subject, array $config = array())
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onUserAfterSave($data, $isNew, $success, $msg)
    {
        if ($success && !$isNew && $this->isInstalled()) {
            $container = SimplerenewFactory::getContainer();
            $account   = $container->getAccount();

            if ($account->validConfiguration()) {
                $user = $container->getUser();

                try {
                    $user->load($data['id']);
                    $account->load($user);

                    // Allow API to turn Joomla name into first/last
                    $account->firstname = null;
                    $account->lastname  = null;
                    $account->setProperties($user->getProperties());

                    $account->save(false);

                } catch (Simplerenew\Exception $e) {
                    // Let this fail silently
                }
            }
        }
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function onUserBeforeDelete(array $data)
    {
        if ($this->isInstalled()) {
            SimplerenewFactory::getContainer()->events->trigger('simplerenewxUserBeforeDelete', array($data['id']));
        }
    }

    /**
     * @param array $data
     * @param bool  $success
     */
    public function onUserAfterDelete(array $data, $success)
    {
        if ($success && $this->isInstalled()) {
            SimplerenewFactory::getContainer()->events->trigger('simplerenewUserAfterDelete', array($data['id']));
        }
    }

    /**
     * Check for component installation and initialise if not
     *
     * @return bool
     */
    protected function isInstalled()
    {
        if (!defined('SIMPLERENEW_LOADED')) {
            $path = JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
            if (!file_exists($path)) {
                JFactory::getApplication()->enqueueMessage(
                    JText::_('PLG_USER_SIMPLERENEW_ERROR_NOT_LOADED'),
                    'error'
                );
                return false;
            }
            require_once $path;
        }
        return true;
    }
}
