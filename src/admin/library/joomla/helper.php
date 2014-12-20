<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewHelper
{
    /**
     * @var JTableExtension
     */
    protected static $extensionTable = null;

    public static function addSubmenu($vName)
    {
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
            // Test Gateway Configuration
            if (self::isConfigured()) {
                try {
                    $valid = SimplerenewFactory::getContainer()->getAccount()->validConfiguration();
                    if (!$valid) {
                        $message->errors[] = JText::_('COM_SIMPLERENEW_ERROR_GATEWAY_CONFIGURATION');
                    }

                } catch (Exception $e) {
                    $message->errors[] = JText::_('COM_SIMPLERENEW_ERROR_GATEWAY_CONFIGURATION');
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
        if (version_compare(JVERSION, '3.0', 'ge')) {
            JHtmlSidebar::addEntry($name, $link, $active);
        } else {
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

        if (!self::isConfigured()) {
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
        $table = self::getExtensionTable();
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
     * @param string $extension
     *
     * @return JTableExtension
     */
    public static function getExtensionTable($extension = 'com_simplerenew')
    {
        if (static::$extensionTable === null) {
            $table = JTable::getInstance('Extension');

            $table->load(array('element' => $extension));
            $table->params = new JRegistry($table->params);

            static::$extensionTable = $table;
        }
        return static::$extensionTable;
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

        ob_start();
        foreach ($results as $result) {
            $content .= JModuleHelper::renderModule($result, $attribs);
        }
        ob_end_clean();

        return $content;
    }

    /**
     * Determine if component has been configured
     *
     * @return bool
     */
    public static function isConfigured()
    {
        $gateway = SimplerenewComponentHelper::getParams()->get('gateway');
        return (bool)$gateway;
    }
}
