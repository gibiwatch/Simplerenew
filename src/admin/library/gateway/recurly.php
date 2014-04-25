<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Configuration;

defined('_JEXEC') or die();

class Recurly extends Gateway
{
    public function __construct()
    {
        require_once __DIR__ . '/recurly/recurly.php';

        $config = new Configuration();
        \Recurly_Client::$apiKey = $config->get('gateway.recurly.apikey');
    }

    public function getAccountCode($userId=null)
    {

    }

    public function getAccount($code=null)
    {

    }

    public function  getPlan($code=null)
    {

    }

    public function getCoupon($code=null)
    {

    }
}
