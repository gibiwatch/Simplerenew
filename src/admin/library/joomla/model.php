<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewModel extends JModelLegacy
{
    public static function addIncludePath($path = '', $prefix = '')
    {
        return parent::addIncludePath($path, $prefix);
    }

    public static function getInstance($type, $prefix = null, $config = array())
    {
        if (empty($prefix)) {
            $prefix = 'SimplerenewModel';
        }
        return parent::getInstance($type, $prefix, $config);
    }
}
