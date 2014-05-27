<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

FOFTemplateUtils::addCSS('media://com_simplerenew/css/backend.css');

try {
    $path = SIMPLERENEW_LIBRARY . '/configuration.json';
    $config = json_decode(file_get_contents($path), true);

    $sr = new \Simplerenew\Factory($config);

    $plan = $sr->getPlan();
    $plan->load('plan-2');


    echo '<pre>';
    echo str_pad(' Plan ', 40, '*', STR_PAD_BOTH) . '<br/>';
    print_r($plan->getProperties());

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
