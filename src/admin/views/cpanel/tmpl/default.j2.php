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
    $config = file_get_contents(SIMPLERENEW_LIBRARY . '/configuration.json');
    $sr     = new \Simplerenew\Factory(json_decode($config, true));

    // Get the user object and load current user
    //$user = $sr->getUser()->load();
    $user     = $sr->getUser();
    $user->id = 31350;

    // Get the account object
    $account = $sr->getAccount()->load($user);

    // Get the billing object
    $billing = $sr->getBilling();
    $billing->load($account);

    echo '<pre>';
    print_r(
        array(
            'Status'   => (int)$account->status,
            'User ID'  => $account->user->id,
            'Code'     => $account->code,
            'Username' => $account->username,
            'Name'     => trim($account->firstname . ' ' . $account->lastname),
            'Billing'  => array(
                'Name'    => trim($billing->firstname . ' ' . $billing->lastname),
                'Country' => $billing->country
            )
        )
    );

    print_r($billing->getProperties());
    echo '</pre>';

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
    if ($e instanceof Simplerenew\Exception) {
        echo '<br/>SIMPLERENEW: ' . $e->getTraceMessage();
    }
}
