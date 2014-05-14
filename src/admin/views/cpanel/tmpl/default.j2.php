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
    $imp = new Simplerenew\Gateway\Recurly\AccountImp();
    $account = new Simplerenew\Api\Account($imp);

    echo '<pre>';
    print_r($account->getProperties());
    echo '</pre>';

} catch (Simplerenew\Exception $e) {
    echo 'ERROR: ' . $e->getTraceMessage();
}
