<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

class plgSystemSimplerenew extends JPlugin
{
    protected static $calls = 0;

    /**
     * @param object $subject
     * @param array  $config
     */
    public function __construct(&$subject, array $config = array())
    {
        parent::__construct($subject, $config);

        $lang = JFactory::getLanguage();
        $lang->load('plg_system_simplerenew', __DIR__);
    }
}