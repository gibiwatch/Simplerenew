<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgSystemSimplerenew extends JPlugin
{
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
    }

    public function onAfterInitialise()
    {
        // Catch push notifications from the gateway and direct to the receiver
        $app = JFactory::getApplication();
        if ($app->isSite()) {
            $uri  = JUri::getInstance();
            $path = rtrim($uri->getPath(), '\\/');
            if ($path == '/simplerenew/notify') {
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

    public function onAfterRoute()
    {
        $this->autoSyncPlans();
    }

    public function onAfterRender()
    {
        $this->onAfterConfigSave();
    }

    /**
     * Automatically update the local plans from the gateway
     *
     * @return void
     */
    protected function autoSyncPlans()
    {
        $app    = JFactory::getApplication();
        $option = $app->input->getCmd('option');
        $view   = $app->input->getCmd('view');

        if (
            $app->isAdmin()
            && $option == 'com_simplerenew'
            && $view == 'plans'
        ) {
            if ($this->isInstalled()) {
                $planSync = abs((int)$this->params->get('planSync', 1)) * 60;

                if ($planSync) {
                    $componentParams = SimplerenewComponentHelper::getParams();
                    $lastPlanSync    = $componentParams->get('log.lastPlanSync', 0);
                    $nextPlanSync    = $lastPlanSync + $planSync;

                    if ($nextPlanSync < time()) {
                        $messages = SimplerenewHelper::syncPlans('ad');
                        SimplerenewHelper::enqueueMessages($messages);
                    }
                }
            }
        }
    }

    /**
     * If we're coming from saving the configuration
     * clear some hidden tracking parameters
     */
    protected function onAfterConfigSave()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin() && $this->isInstalled()) {

            $option    = $app->input->getCmd('option');
            $component = $app->input->getCmd('component');
            $task      = $app->input->getCmd('task');

            if (
                $option == 'com_config' &&
                $component == 'com_simplerenew' &&
                strpos($task, 'component.save')
            ) {
                $table  = SimplerenewHelper::getExtensionTable();
                $params = $table->params()->toArray();
                if (isset($params['log'])) {
                    unset($params['log']);

                    $params        = new JRegistry($params);
                    $table->params = $params->toString();
                    $table->store();
                }
            }
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
                    JText::_('PLG_SYSTEM_SIMPLERENEW_ERROR_NOT_LOADED'),
                    'error'
                );
                return false;
            }
            require_once $path;
        }

        // Make sure component language file loaded
        SimplerenewFactory::getLanguage()
            ->load('com_simplerenew', SIMPLERENEW_ADMIN);

        return true;
    }
}
