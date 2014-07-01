<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends SimplerenewControllerBase
{
    protected $default_view = 'subscribe';

    public function test()
    {
        $user = SimplerenewFactory::getContainer()->getUser()->loadByUsername('fred');
        $password = 'xyzzy';

        try {
            $user->login($password, true);
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage() . ' (' . $e->getCode() . ')';
        }

        echo '<br/><br/>TEST: ' . $user->validate($password);
        echo '<pre>';
        print_r($user->getProperties());
        echo '</pre>';

    }
}
