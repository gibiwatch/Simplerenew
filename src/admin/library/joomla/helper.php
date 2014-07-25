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
                    $target = & $source[$tree[0]];
                    for ($i = 1; $i < count($tree) - 1; $i++) {
                        $target = & $target[$tree[$i]];
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
     * Return a messaging object with Simplerenew warnings and errors
     *
     * @return object
     */
    public static function getNotices()
    {
        $errors   = array();
        $warnings = array();

        try {
            $plan = SimplerenewFactory::getContainer()->getPlan();
            if (!$plan->validConfiguration()) {
                $errors[] = JText::_('COM_SIMPLERENEW_ERROR_GATEWAY_CONFIGURATION');
            }

        } catch (Exception $e) {
            $errors[] = JText::_('COM_SIMPLERENEW_ERROR_GATEWAY_CONFIGURATION');
        }

        return (object)array(
            'errors'   => $errors,
            'warnings' => $warnings
        );
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
                $app->enqueueMessage($warning, 'notice');
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
        $params  = SimplerenewComponentHelper::getParams('com_simplerenew');

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
        if ($defaultGroup <= 0) {
            $message->errors[] = JText::_('COM_SIMPLERENEW_ERROR_DEFAULTGROUP');
            return $message;
        }

        // Update/Add plans found on the gateway

        /** @var Simplerenew\Api\Plan $plan */
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
                        'published'        => 1,
                        'alias'            => SimplerenewApplicationHelper::stringURLSafe($plan->code),
                        'created_by_alias' => JText::_('COM_SIMPLERENEW_PLAN_SYNC_IMPORTED')
                    )
                );
            }
            $plansTable->bind($plan->getProperties());

            if ($plansTable->group_id <= 0) {
                $plansTable->group_id = $defaultGroup;
            }

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
        $table->params = $params->toString();
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
}
