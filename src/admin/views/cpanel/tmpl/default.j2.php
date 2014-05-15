<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api;
use Simplerenew\Gateway;

defined('_JEXEC') or die();

FOFTemplateUtils::addCSS('media://com_simplerenew/css/backend.css');

try {
    $config = new Simplerenew\Configuration(SIMPLERENEW_LIBRARY . '/configuration.json', true);
    $sr = new \Simplerenew\Factory($config);

    // Get the user object and load current user
    $user = $sr->getUser()->load();

    // Get the account object
    $account = $sr->getAccount();

    echo '<pre>';
    print_r(
        array(
            $user->fullname,
            $user->lastname . ', ' . $user->firstname
        )
    );
    print_r($account->load($user));
    echo '</pre>';
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
    if ($e instanceof Simplerenew\Exception) {
        echo '<br/>SIMPLERENEW: ' . $e->getTraceMessage();
    }
}
