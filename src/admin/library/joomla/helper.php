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
    public static function addSubmenu($vName)
    {
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
     *
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

        return array(
            'errors' => $errors,
            'warnings' => $warnings
        );
    }

    public static function enqueueNotices()
    {
        $app = SimplerenewFactory::getApplication();
        $notices = self::getNotices();

        foreach ($notices['errors'] as $error) {
            $app->enqueueMessage($error, 'error');
        }

        foreach ($notices['warnings'] as $warning) {
            $app->enqueueMessage($warning, 'notice');
        }

        return (bool)($notices['errors'] || $notices['warnings']);
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
}
