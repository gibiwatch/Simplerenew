<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

defined('_JEXEC') or die();

abstract class Gateway
{
    public static function getInstance($gateway) {
        $class = 'Simplerenew\\Gateway\\' . ucfirst(strtolower($gateway));
        if (class_exists($class)) {
            return new $class();
        }
        throw new \Exception('unknown gateway ' . $class);
    }

    abstract public function getAccountCode($id);

    abstract public function getAccount($id=null);

    abstract public function getPlan($code=null);

    abstract public function getCoupon($code=null);
}
