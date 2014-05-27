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
    $account = $sr->getAccount()->load($user)->reopen();

    // Get the billing object
    $billing = $sr->getBilling();
    $billing->load($account);
    if (false) {
        $billing->phone = '123.123.1234';
        $billing->setCreditCard(
            new \Simplerenew\Primitive\CreditCard(
                array(
                    'number' => '4111111111111111',
                    'month'  => 1,
                    'year'   => 2016,
                    'ccv'    => 123
                )
            )
        );
        $billing->save();
    } else {
        //$billing->delete();
        //$billing->setPayment(null);
    }


    echo '<pre>';
    //echo str_pad(' User ', 40, '*', STR_PAD_BOTH) . '<br/>';
    //print_r($user->getProperties());

    echo str_pad(' Account ', 40, '*', STR_PAD_BOTH) . '<br/>';
    print_r($account->getProperties());
    echo $account->address . "\n\n";

    echo str_pad(' Billing ', 40, '*', STR_PAD_BOTH) . '<br/>';
    echo $billing->address;
    print_r($billing->getProperties());
    print_r($billing->payment->getProperties());
    echo $billing->address . "\n\n";
    echo '</pre>';



} catch (Simplerenew\Exception $e) {
    echo '<br/>SIMPLERENEW: ' . $e->getTraceMessage() . '<br/>';
    echo 'Previous: ' . get_class($e->getPrevious()) . '<br/>';

    echo '<pre>';
    print_r($e->getCallStack());
    echo '</pre>';

} catch (\Recurly_Error $e) {
    echo '<br/>' . get_class($e) . ': ' . $e->getMessage();

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
