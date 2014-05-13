<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\User\User;

defined('_JEXEC') or die();

FOFTemplateUtils::addCSS('media://com_simplerenew/css/backend.css');

try {
    $data = json_decode(file_get_contents(SIMPLERENEW_LIBRARY . '/configuration.json'), true);

    $sr = new Simplerenew\Factory('recurly', $data['gateway']['test']);

    //$di = new Simplerenew\DI\Pimple(json_decode($data, true));
    //$config = new Simplerenew\Configuration($data);
    //$account = new Simplerenew\Gateway\Recurly\Account($config);

    echo '<pre>';
    print_r($data);
    print_r($sr);
    //print_r($di->offsetGet('account'));
    //print_r($di);
    //print_r($account->getProperties());
    echo '</pre>';

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
