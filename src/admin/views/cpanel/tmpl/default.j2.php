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
    $path = SIMPLERENEW_LIBRARY . '/configuration.json';
    $config = json_decode(file_get_contents($path), true);

    $sr = new \Simplerenew\Factory($config);

    $user = $sr->getUser()->load();

    // Get the account object
    $account = $sr->getAccount()->load($user);
    $account->company = 'Grumpy Engineering';
    $account->save();

    // Get the billing object
    //$billing = $sr->getBilling();
    //$billing->load($account);

    echo '<pre>';
    echo str_pad(' User ', 40, '*', STR_PAD_BOTH) . '<br/>';
    print_r($user->getProperties());

    echo str_pad(' Account ', 40, '*', STR_PAD_BOTH) . '<br/>';
    print_r($account->getProperties());

    echo '</pre>';

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
    if ($e instanceof Simplerenew\Exception) {
        echo '<br/>SIMPLERENEW: ' . $e->getTraceMessage();
    }
}
