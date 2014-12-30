<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgSystemSimplerenew extends JPlugin
{
    /**
     * @var array view/layouts enforcing SSL
     *            Format: array(<view> => [array(<layout1>, <layout2>...)])
     *            Use array('*') to select all layouts for a view
     */
    protected $sslViews = array(
        'account'   => array('edit'),
        'subscribe' => array('*')
    );

    /**
     * @var JTableExtension
     */
    protected $extensionTable = null;

    /**
     * @param object $subject
     * @param array  $config
     */
    public function __construct(&$subject, array $config = array())
    {
        parent::__construct($subject, $config);

        $this->loadLanguage();
        $this->params = JComponentHelper::getParams('com_simplerenew');
    }

    public function onAfterInitialise()
    {
        $this->catchNotify();
    }

    public function onAfterRoute()
    {
        $this->redirectRegister();
        $this->revertSSL();
        $this->autoSyncPlans();
    }

    /**
     * Automatically update the local plans from the gateway
     *
     * @return void
     */
    protected function autoSyncPlans()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin() && $this->isInstalled()) {
            $option    = $app->input->getCmd('option');
            $view      = $app->input->getCmd('view', 'plans');
            $component = $app->input->getCmd('component');

            if ($option == 'com_simplerenew' && $view == 'plans') {
                $planSync = abs((int)$this->params->get('advanced.planSync', 1)) * 60;

                if ($planSync) {
                    $lastPlanSync = $this->params->get('log.lastPlanSync', 0);
                    $nextPlanSync = $lastPlanSync + $planSync;

                    if ($nextPlanSync < time()) {
                        $messages = SimplerenewHelper::syncPlans('ad');
                        SimplerenewHelper::enqueueMessages($messages);
                    }
                }
            } elseif ($option == 'com_config' && $component == 'com_simplerenew') {
                $table = SimplerenewHelper::getExtensionTable();
                $table->params->set('log.lastPlanSync', 0);
                $table->params = $table->params->toString();
                $table->store();
            }
        }
    }

    /**
     * clear hidden log parameter
     */
    protected function clearLastSync()
    {
        $app       = JFactory::getApplication();
        $option    = $app->input->getCmd('option');
        $component = $app->input->getCmd('component');

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
                    JText::_('PLG_SYSTEM_SIMPLERENEW_ERROR_NOT_LOADED'),
                    'error'
                );
                return false;
            }
            require_once $path;

            // Make sure component language file loaded
            SimplerenewFactory::getLanguage()
                ->load('com_simplerenew', SIMPLERENEW_ADMIN);
        }

        return true;
    }

    /**
     * Catch notification messages from the gateway
     *
     * @throws Exception
     * @return void
     */
    protected function catchNotify()
    {
        $app = JFactory::getApplication();
        if ($app->isSite() && $this->isInstalled()) {
            $uri = JUri::getInstance();
            if (preg_match('#/simplerenew/notify/?$#', $uri->getPath())) {
                $vars = array(
                    'option' => 'com_simplerenew',
                    'task'   => 'notify.receive',
                    'format' => 'raw'
                );
                foreach ($vars as $var => $value) {
                    $app->input->set($var, $value);
                }
                if ($app->getRouter()->getMode() == JROUTER_MODE_SEF) {
                    $uri->setPath('/component/simplerenew');
                    $uri->setQuery($vars);
                }
            }
        }
    }

    /**
     * When desired, switch back to http after coming from a Simplerenew SSL form
     *
     * @throws Exception
     * @return void
     */
    protected function revertSSL()
    {
        $app = JFactory::getApplication();

        $revert = $this->params->get('advanced.revertSSL', 0);
        if ($app->isSite() && $revert) {
            $uri = JUri::getInstance();

            if ($uri->getScheme() == 'https') {
                $option = $app->input->getCmd('option');
                $view   = $app->input->getCmd('view');
                $layout = $app->input->getCmd('layout');

                $sslView = isset($this->sslViews[$view]) ? $this->sslViews[$view] : array();
                $target  = array_intersect($sslView, array('*', $layout));

                if ($option == 'com_simplerenew' && $target) {
                    $app->setUserState('simplerenew.ssl.reset', true);

                } else {
                    $menu  = $app->getMenu()->getActive();
                    $reset = !$target
                        && $menu
                        && $menu->params->get('secure') != 1
                        && $app->getUserState('simplerenew.ssl.reset', false)
                        && $app->input->getCmd('format', 'html') == 'html'
                        && $app->input->getMethod() != 'POST'
                        && $app->input->getCmd('tmpl', '') != 'component';

                    if ($reset) {
                        $app->setUserState('simplerenew.ssl.reset', false);
                        $uri->setScheme('http');
                        $app->redirect((string)$uri);
                    }
                }
            }
        }
    }

    /**
     * Catch attempts to go to standard Joomla registration form if requested
     *
     * @throws Exception
     */
    protected function redirectRegister()
    {
        $app    = JFactory::getApplication();
        $option = $app->input->getCmd('option');
        $view   = $app->input->getCmd('view');
        if (
            $app->isSite()
            && $option == 'com_users'
            && $view == 'registration'
            && $this->params->get('advanced.disableRegistration', 1)
            && $this->isInstalled()
        ) {
            $app->redirect(JRoute::_(SimplerenewRoute::get('subscribe')));
        }
    }
}
