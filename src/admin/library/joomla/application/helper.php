<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewApplicationHelper extends JApplicationHelper
{
    public static function stringURLSafe($string)
    {
        if (method_exists('JApplicationHelper', 'stringURLSafe')) {
            return parent::stringURLSafe($string);
        }

        // Deprecated in later Joomla versions
        return JApplication::stringURLSafe($string);
    }
}
