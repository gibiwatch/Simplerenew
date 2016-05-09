<?php
/**
 * @package    Simplerenew
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerUpdate extends SimplerenewControllerBase
{
    public function update()
    {
        $app = SimplerenewFactory::getApplication();
        $message = JText::_('JERROR_ALERTNOAUTHOR');

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = new JURI($_SERVER['HTTP_REFERER']);

            if (
                $app->input->getMethod() == 'GET'
                && $referer->getHost() == $_SERVER['SERVER_NAME']
                && $referer->getVar('option') == 'com_simplerenew'
            ) {
                $message = JText::_('COM_SIMPLERENEW_UPDATE_NONE');
                $update = SimplerenewFactory::getStatus()->update;
                if ($update) {
                    $model = SimplerenewModel::getInstance('Update');
                    $model->update(array($update->update_id));
                    $message = $model->getState('result') ? null : JText::_('COM_SIMPLERENEW_UPDATE_FAILED');
                }
            }
        } else {
            $referer = 'index.php?option=com_simplerenew';
        }

        $this->setRedirect((string)$referer, $message);
    }
}
