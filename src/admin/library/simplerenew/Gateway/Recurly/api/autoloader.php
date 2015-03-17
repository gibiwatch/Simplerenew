<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

RecurlyLoader::register();

abstract class RecurlyLoader
{
    protected static $exceptions = array(
        'Recurly_CouponRedemption' => 'redemption.php',
        'Recurly_ClientResponse'   => 'response.php',
        'Recurly_HmacHash'         => 'util/hmac_hash.php',
        'Recurly_TransactionError' => 'transaction_error.php'
    );

    public static function register()
    {
        spl_autoload_register(array('\\RecurlyLoader', 'load'), true);
    }

    protected static function load($class)
    {
        if (!class_exists($class) && strpos($class, 'Recurly_') === 0) {
            if (array_key_exists($class, static::$exceptions)) {
                $file = static::$exceptions[$class];
            } elseif (strpos($class, 'Error') == (strlen($class) - 5)) {
                $file = 'errors.php';
            } else {
                list(, $file) = explode('_', $class, 2);
                $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', $file);
                $file  = strtolower(join('_', $parts)) . '.php';
            }
            $file = __DIR__ . '/recurly/' . $file;

            if (file_exists($file)) {
                require_once $file;
                return $file;
            }
        }
        return false;
    }
}
