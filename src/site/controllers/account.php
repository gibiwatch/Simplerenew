<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerAccount extends SimplerenewControllerBase
{
    public function save()
    {
        $this->checkToken();

        echo '<p>testing in progress</p>';

        echo '<pre>';
        print_r($_REQUEST);
        echo '</pre>';
    }
}
