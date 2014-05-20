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
    $config = array(
        'account' => array(
            'codeMask' => 'OS_%s'
        ),

        'user' => array(
            'adapter' => 'joomla'
        ),

        'gateway' => array(
            'name' => 'recurly',
            'mode' => 'live',
            'test' => array(
                'apikey' => '6d00ae5e11894d1581830bcc8deb8778',
                'private' => '699d2b94ab364f9594e41a7d2e5ee1c7'
            ),
            'live' => array(
                'apikey' => '808896419fd94121ba4bbcb0f32f460b',
                'private' => 'f284ad043e784180b97661881fb459da'
            )
        )
    );

    $sr = new \Simplerenew\Factory($config);

    $user = $sr->getUser();
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
