<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelper
{
    /**
     * Build the submenu in admin if needed. Triggers the
     * onAdminSubmenu event for component addons to attach
     * their own admin screens.
     *
     * The expected responses must be an array or an array of arrays
     * in the form:
     * [
     *    'text' => Static language string,
     *    'link' => Link to the screen
     *    'view' => unique view name
     * ]
     *
     * @param $vName
     *
     * @return void
     */
    public static function addSubmenu($vName)
    {
        $events = SimplerenewFactory::getContainer()->getEvents();
        if ($results = array_filter($events->trigger('simplerenewAdminSubmenu'))) {
            $subMenus = array(
                array(
                    'text' => 'COM_SIMPLERENEW_SUBMENU_PLANS',
                    'link' => 'index.php?option=com_simplerenew&view=plans',
                    'view' => 'plans'
                )
            );

            $base = array(
                'text' => null,
                'link' => null,
                'view' => null
            );

            foreach ($results as $result) {
                if ($newMenu = array_intersect_key($result, $base)) {
                    $subMenus[] = $result;
                } elseif (is_array($result)) {
                    foreach ($result as $subMenu) {
                        if ($newMenu = array_intersect_key($subMenu, $base)) {
                            $subMenus[] = $subMenu;
                        }
                    }
                }
            }

            foreach ($subMenus as $subMenu) {
                if (is_array($subMenu)) {
                    static::addMenuEntry(
                        JText::_($subMenu['text']),
                        $subMenu['link'],
                        $vName == $subMenu['view']
                    );
                }
            }
        }
    }

    /**
     * get component information
     *
     * @return JRegistry
     */
    public static function getInfo()
    {
        $info = new jRegistry();
        $path = SIMPLERENEW_ADMIN . '/simplerenew.xml';
        if (file_exists($path)) {
            $xml = JFactory::getXML($path);

            foreach ($xml->children() as $e) {
                if (!$e->children()) {
                    $info->set($e->getName(), (string)$e);
                }
            }
        }
        return $info;
    }

    /**
     * Save submitted form data in the session.
     * NOTE: No security is employed here. Caller should be
     * careful not to store sensitive data in the session.
     * Avoid storing passwords and credit card data.
     *
     * @param string $domain
     * @param array  $exclude
     * @param array  $source
     *
     * @return void
     */
    public static function saveFormData($domain, array $exclude = null, array $source = null)
    {
        $domain = 'simplerenew.' . $domain;

        if ($source === null) {
            $source = $_POST;
        }
        $filter = SimplerenewFilterInput::getInstance();
        $source = $filter->clean($source, 'array_keys');

        if ($exclude) {
            foreach ($exclude as $key) {
                if (strpos($key, '.') === false) {
                    if (isset($source[$key])) {
                        unset($source[$key]);
                    }
                } else {
                    $tree   = explode('.', $key);
                    $target = &$source[$tree[0]];
                    for ($i = 1; $i < count($tree) - 1; $i++) {
                        $target = &$target[$tree[$i]];
                    }
                    if (isset($target[$tree[$i]])) {
                        unset($target[$tree[$i]]);
                    }
                }
            }
        }

        $app = SimplerenewFactory::getApplication();
        $app->setUserState($domain, base64_encode(serialize($source)));
    }

    /**
     * Retrieve previously saved form data to repopulate forms
     *
     * @param string $domain
     * @param bool   $clear
     *
     * @return array
     */
    public static function loadFormData($domain, $clear = true)
    {
        $domain   = 'simplerenew.' . $domain;
        $app      = SimplerenewFactory::getApplication();
        $formData = unserialize(base64_decode($app->getUserState($domain)));

        if ($clear) {
            $app->setUserState($domain, null);
        }
        return $formData;
    }

    /**
     * Return a messaging object with Simplerenew warnings and errors.
     * Only meant for use in backend/admin
     *
     * @return object
     */
    public static function getNotices()
    {
        $message = (object)array(
            'errors'   => array(),
            'warnings' => array()
        );

        if (SimplerenewFactory::getApplication()->isAdmin()) {
            $status = SimplerenewFactory::getStatus();
            if (!$status->configured) {
                $message->errors[] = JText::_('COM_SIMPLERENEW_ERROR_GATEWAY_CONFIGURATION');
            }

            if (!$status->gateway) {
                $gateway = SimplerenewFactory::getContainer()->gateway;
                if (!JPluginHelper::isEnabled('simplerenew', strtolower($gateway))) {
                    $message->errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_GATEWAY_PLUGIN_UNAVAILABLE', $gateway);
                } else {
                    $message->errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_GATEWAY_MISCONFIGURED', $gateway);
                }
            }

            // Check critical support plugins
            if (!JPluginHelper::isEnabled('user', 'simplerenew')) {
                $message->errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_PLUGIN_MISSING', 'user/simplerenew');
            }
            if (!JPluginHelper::isEnabled('system', 'simplerenew')) {
                $message->errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_PLUGIN_MISSING', 'system/simplerenew');
            }

            // Check user group settings
            $params          = SimplerenewComponentHelper::getParams();
            $defaultGroup    = $params->get('basic.defaultGroup');
            $expirationGroup = $params->get('basic.expirationGroup');

            if ($defaultGroup == $expirationGroup) {
                $message->warnings[] = JText::_('COM_SIMPLERENEW_WARN_GROUPS_EQUAL');
            }

            $db = SimplerenewFactory::getDbo();

            // Check for plans set to the expiration group
            $plans = $db
                ->setQuery('Select name From #__simplerenew_plans Where group_id=' . $db->quote($expirationGroup))
                ->loadColumn();

            if (count($plans) > 1) {
                $message->warnings[] = JText::sprintf('COM_SIMPLERENEW_WARN_PLANS_EXPIRATION_GROUP', count($plans));
            } elseif (count($plans) > 0) {
                $message->warnings[] = JText::sprintf('COM_SIMPLERENEW_WARN_PLANS_EXPIRATION_GROUP_1', $plans[0]);
            }

            // Check for plans with no subscription group
            $plans = $db
                ->setQuery('Select name From #__simplerenew_plans Where group_id=0')
                ->loadColumn();
            if (count($plans) > 1) {
                $message->warnings[] = JText::sprintf('COM_SIMPLERENEW_WARN_PLANS_NO_GROUP', count($plans));
            } elseif (count($plans) > 0) {
                $message->warnings[] = JText::sprintf('COM_SIMPLERENEW_WARN_PLANS_NO_GROUP_1', $plans[0]);
            }
        }

        return $message;
    }

    /**
     * Queue errors, warnings and messages from a messaging object
     *
     * @param object $messages
     */
    public static function enqueueMessages($messages)
    {
        $app = SimplerenewFactory::getApplication();

        if (!empty($messages->errors)) {
            foreach ($messages->errors as $error) {
                $app->enqueueMessage($error, 'error');
            }
        }

        if (!empty($messages->warnings)) {
            foreach ($messages->warnings as $warning) {
                $app->enqueueMessage($warning, 'warning');
            }
        }

        if (!empty($messages->success)) {
            foreach ($messages->success as $message) {
                $app->enqueueMessage($message);
            }
        }
    }

    /**
     * @param string $name
     * @param string $link
     * @param bool   $active
     *
     * @return void
     */
    protected static function addMenuEntry($name, $link, $active = false)
    {
        if (method_exists('JHtmlSidebar', 'addEntry')) {
            JHtmlSidebar::addEntry($name, $link, $active);
        } else {
            // Deprecated after J2.5
            JSubMenuHelper::addEntry($name, $link, $active);
        }
    }

    /**
     * Update local plans from gateway plans
     *
     * @param string $show
     *
     * @return object
     */
    public static function syncPlans($show = 'uad')
    {
        // Set messaging for return
        $message = (object)array(
            'errors'  => array(),
            'success' => array()
        );

        $status = SimplerenewFactory::getStatus();
        if (!$status->configured) {
            return $message;
        }

        $params = SimplerenewComponentHelper::getParams('com_simplerenew');
        try {
            $plansGateway = SimplerenewFactory::getContainer()->getPlan();
            $plansRemote  = $plansGateway->getList();
        } catch (Exception $e) {
            $message->errors[] = JText::sprintf('COM_SIMPLERENEW_ERROR_GATEWAY_FAILURE', $e->getMessage());
            return $message;
        }

        $plansTable = SimplerenewTable::getInstance('Plans');

        $db         = SimplerenewFactory::getDbo();
        $query      = $db->getQuery(true)
            ->select('*')
            ->from('#__simplerenew_plans');
        $plansLocal = $db->setQuery($query)->loadAssocList('code');

        // Identify local plans not on the gateway
        $plansDisable = array();
        $plansUpdate  = array();
        foreach ($plansLocal as $code => $plan) {
            // Need to translate from db field to Plan property
            $plan['group'] = $plan['group_id'];
            unset($plan['group_id']);
            if (!array_key_exists($code, $plansRemote)) {
                if ($plan['published']) {
                    $plansDisable[] = $plan['id'];
                }
            } else {
                $plansUpdate[$code] = $plan;
            }
        }

        // Unpublish any plans not on the gateway
        if ($plansDisable) {
            /** @var SimplerenewModelPlan $planModel */
            $planModel = SimplerenewModel::getInstance('Plan');
            $planModel->publish($plansDisable, 0);
        }

        // Load the default group in case we add plans from the gateway
        $defaultGroup = $params->get('basic.defaultGroup');

        // Update/Add plans found on the gateway
        /** @var Simplerenew\Api\Plan $plan */

        $nextOrder = $plansTable->getNextOrder();
        foreach ($plansRemote as $code => $plan) {
            if (array_key_exists($code, $plansUpdate)) {
                // Refresh old plan if changes found
                if ($plan->equals($plansUpdate[$code])) {
                    continue;
                }
                $plansTable->bind($plansUpdate[$code]);
            } else {
                // Add new plan
                $plansTable->setProperties(
                    array(
                        'id'               => null,
                        'group_id'         => $plansTable->group_id ?: $defaultGroup,
                        'ordering'         => $nextOrder++,
                        'created_by_alias' => JText::_('COM_SIMPLERENEW_PLAN_SYNC_IMPORTED')
                    )
                );
                $plansTable->published = (int)(bool)$plansTable->group_id;
            }

            $plansTable->bind($plan->getProperties());
            if (!$plansTable->store()) {
                $message->errors = array_merge(
                    $message->errors,
                    $plansTable->getErrors()
                );
            }
        }

        // Update Sync time
        $table = static::getExtensionTable();
        $table->params->set('log.lastPlanSync', time());
        $table->params = $table->params->toString();
        $table->store();

        // Fill out messaging object
        $updated = count($plansUpdate);
        if (stripos($show, 'u') !== false && $updated) {
            $message->success[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_UPDATED', $updated);
        }

        if (stripos($show, 'a') !== false && ($added = count($plansRemote) - $updated)) {
            $message->success[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_ADDED', $added);
        }

        if (stripos($show, 'd') !== false && ($disabled = count($plansDisable))) {
            $message->success[] = JText::plural('COM_SIMPLERENEW_PLANS_N_ITEMS_DISABLED', $disabled);
        }

        if (!$show && !count($message->success)) {
            $message->success[] = JText::_('COM_SIMPLERENEW_PLANS_NOSYNC');
        }

        return $message;
    }

    /**
     * Get the Extension table entry
     *
     * @param string $element
     * @param string $folder
     * @param string $type
     *
     * @return JTableExtension
     */
    public static function getExtensionTable($element = 'com_simplerenew', $folder = null, $type = null)
    {
        $table = JTable::getInstance('Extension');

        $query = array_filter(array(
            'element' => $element,
            'folder'  => $folder,
            'type'    => $type
        ));

        $table->load($query);
        $table->params = new JRegistry($table->params);

        return $table;
    }

    /**
     * Render the modules in a position
     *
     * @param string $position
     * @param mixed  $attribs
     *
     * @return string
     */
    public static function renderModule($position, $attribs = array())
    {
        $results = JModuleHelper::getModules($position);
        $content = '';

        if (is_string($attribs)) {
            $attribs = JUtility::parseAttributes($attribs);
        }
        if (!isset($attribs['style'])) {
            $attribs['style'] = 'xhtml';
        }

        ob_start();
        foreach ($results as $result) {
            $content .= JModuleHelper::renderModule($result, $attribs);
        }
        ob_end_clean();

        return $content;
    }

    /**
     * Get the requested application object
     *
     * @param $client
     *
     * @return JApplicationCms
     */
    public static function getApplication($client)
    {
        if (class_exists('JApplicationCms')) {
            return JApplicationCms::getInstance($client);
        }

        // Deprecated in later Joomla versions
        return JApplication::getInstance($client);
    }

    /**
     * Make sure the appropriate component language files are loaded
     *
     * @param string $option
     * @param string $adminPath
     * @param string $sitePath
     *
     * @return void
     * @throws Exception
     */
    public static function loadOptionLanguage($option, $adminPath, $sitePath)
    {
        $app = SimplerenewFactory::getApplication();
        if ($app->input->getCmd('option') != $option) {
            switch (JFactory::getApplication()->getName()) {
                case 'administrator':
                    SimplerenewFactory::getLanguage()->load($option, $adminPath);
                    break;

                case 'site':
                    SimplerenewFactory::getLanguage()->load($option, $sitePath);
                    break;
            }
        }
    }

    /**
     * Get active cancellation funnels
     *
     * @param JRegistry $params
     *
     * @return JRegistry
     */
    public static function getFunnel(JRegistry $params = null)
    {
        $result = new JRegistry();

        if (!$params) {
            // Use parameters from the currently active menu
            $menu = SimplerenewFactory::getApplication()->getMenu()->getActive();
            if ($menu && $menu->type == 'component') {
                $params = $menu->params;
            }
        }
        if ($params && ($funnel = $params->get('funnel'))) {
            $funnel = array_filter(is_object($funnel) ? get_object_vars($funnel) : $funnel);

            $funnel['enabled'] = !empty($funnel['enabled']) && $funnel['enabled'] && count($funnel) > 1;
            $result->loadArray($funnel);
        }
        return $result;
    }
}
