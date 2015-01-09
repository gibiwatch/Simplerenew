<?php
/**
 * @package    Simplerenew
 * @subpackage
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2014 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerUpdate extends SimplerenewControllerBase
{
    public function update()
    {
        $app = SimplerenewFactory::getApplication();
        $msg = null;

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = new JURI($_SERVER['HTTP_REFERER']);

            if (
                $app->input->getMethod() == 'GET'
                && $referer->getHost() == $_SERVER['HTTP_HOST']
                && $referer->getVar('option') == 'com_simplerenew'
            ) {
                $update = SimplerenewFactory::getStatus()->update;
                if ($update) {
                    $model = SimplerenewModel::getInstance('Update');
                    $model->update(array($update->update_id));
                }
            }
        } else {
            $referer = 'index.php?option=com_simplerenew';
        }

        $this->setRedirect((string)$referer);
    }
}