<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewModel extends JModelLegacy
{
    /**
     * @var bool
     */
    private static $pathAdded = false;

    public static function addIncludePath($path = '', $prefix = '')
    {
        return parent::addIncludePath($path, $prefix);
    }

    public static function getInstance($type, $prefix = 'SimplerenewModel', $config = array())
    {
        if ($prefix == 'SimplerenewModel' && !static::$pathAdded) {
            parent::addIncludePath(SIMPLERENEW_ADMIN . '/models');
            static::$pathAdded = true;
        }
        return parent::getInstance($type, $prefix, $config);
    }
}
