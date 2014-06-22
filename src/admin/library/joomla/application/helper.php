<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewApplicationHelper extends JApplicationHelper
{
    public static function stringURLSafe($string)
    {
        if (version_compare(JVERSION, '3.0', 'ge')) {
            return parent::stringURLSafe($string);
        } else {
            return JApplication::stringURLSafe($string);
        }
    }
}
