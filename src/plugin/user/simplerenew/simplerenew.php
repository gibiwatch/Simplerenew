<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
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
            $account = $container->getAccount();

            if ($account->validConfiguration()) {
                $user = $container->getUser();

                try {
                    $user->load($data['id']);
                    $account->load($user);

                    // Allow API to turn Joomla name into first/last
                    $account->firstname = null;
                    $account->lastname = null;
                    $account->setProperties($user->getProperties());

                    $account->save(false);

                } catch (NotFound $e) {
                    // Non-subscription user can be ignored

                } catch (Simplerenew\Exception $e) {
                    SimplerenewFactory::getApplication()
                        ->enqueueMessage(
                            JText::sprintf('COM_SIMPLERENEW_ERROR_ACCOUNT_SAVE', $e->getMessage()),
                            'notice'
                        );
                }
            }
        }
    }

    public function onUserAfterDelete($user, $success, $msg)
    {
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
