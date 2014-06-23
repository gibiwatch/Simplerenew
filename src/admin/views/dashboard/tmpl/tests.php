<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

try {
    $sr = SimplerenewHelper::getSimplerenew();

    $plan = $sr->getPlan();
//    $user = $sr->getUser()->loadByUsername('bill');
//    $user2 = $sr->getUser()->loadByUsername('fred');

//    $account = $sr->getAccount()->load($user);
//    $billing = $sr->getBilling()->load($account);

    echo '<pre>';
    echo str_pad(' Plan List ', 40, '*', STR_PAD_BOTH) . '<br/>';

    /** @var Simplerenew\Api\Plan $item */
    $planList = $plan->getList();
    foreach ($planList as $code => $item) {
        echo '<br/>**' . $code . '<br/>';
        echo '<pre>';
        print_r($item->getProperties());
        echo '</pre>';
    }

//    echo str_pad(' User ', 40, '*', STR_PAD_BOTH) . '<br/>';
//    print_r($user->getProperties());
//    print_r($user2->getProperties());

//    echo str_pad(' Account ', 40, '*', STR_PAD_BOTH) . '<br/>';
//    print_r($account->getProperties());

//    echo str_pad(' Billing ', 40, '*', STR_PAD_BOTH) . '<br/>';
//    print_r($billing->getProperties());
//    print_r($billing->payment->getProperties());

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
