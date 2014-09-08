<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends SimplerenewControllerBase
{
    protected $default_view = 'plans';

    public function __construct($config = array())
    {
        parent::__construct($config);

        if (!SimplerenewHelper::isConfigured()) {
            SimplerenewFactory::getApplication()->input->set('view', 'welcome');
        } else {
            $notices = SimplerenewHelper::getNotices();
            SimplerenewHelper::enqueueMessages($notices);
        }
    }
}
