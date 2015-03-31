<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlUser
{
    public static function displayname($firstName, $lastName = null, $username = null)
    {
        if (func_num_args() == 1) {
            if (is_object($firstName)) {
                $firstName = get_object_vars($firstName);
            }
            if (!is_array($firstName)) {
                return (string)$firstName;
            }

            $username = isset($firstName['username']) ? $firstName['username'] : '';
            $lastName = isset($firstName['lastname']) ? $firstName['lastname'] : '';
            $firstName = isset($firstName['firstname']) ? $firstName['firstname'] : '';
        }

        return trim($firstName . ' ' . $lastName)
            . ($username ? ' (' . $username . ')' : '');
    }
}
