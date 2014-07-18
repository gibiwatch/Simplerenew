<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerPlans extends SimplerenewControllerAdmin
{
    protected $text_prefix = 'COM_SIMPLERENEW_PLANS';

    public function getModel($name = 'Plan', $prefix = 'SimplerenewModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function sync()
    {
        $this->checkToken();

        $messages = SimplerenewHelper::syncPlans(true);
        SimplerenewHelper::enqueueMessages($messages);

        $this->setRedirect('index.php?option=com_simplerenew&view=plans');
    }
}
