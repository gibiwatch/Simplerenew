<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerBase extends JControllerLegacy
{
    /**
     * Standard form token check and redirect
     *
     * @return void
     */
    protected function checkToken()
    {
        if (!JSession::checkToken()) {
            $home = SimplerenewFactory::getApplication()->getMenu()->getDefault();

            SimplerenewFactory::getApplication()->redirect(
                JRoute::_('index.php?Itemid=' . $home->id),
                JText::_('JINVALID_TOKEN'),
                'error'
            );
        }
    }

    /**
     * Standard return to calling url. In order:
     *    - Looks for base64 encoded 'return' URL variable
     *    - Uses current 'Itemid' URL variable
     *    - Uses current 'option', 'view'/'task', 'layout' URL variables
     *    - Goes to site default page
     *
     * @param null $message
     * @param null $type
     *
     * @return true
     */
    protected function callerReturn($message = null, $type = null)
    {
        $app = SimplerenewFactory::getApplication();
        if ($url = $app->input->getBase64('return')) {
            $url = base64_decode($url);
        } else {
            $itemid = $app->input->getInt('Itemid');

            $url = new JURI('index.php');
            if ($itemid) {
                $menu = $app->getMenu()->getItem($itemid);
                $url->setVar('Itemid', $itemid);
            } else {
                $menu = $app->getMenu()->getDefault();
            }

            if ($option = $app->input->getCmd('option')) {
                $url->setVar('option', $option);
            }

            $view = $app->input->getCmd('view');
            $menuView = @$menu->query['view'] ? : null;
            if (!empty($view) && (empty($menuView) || ($view != $menuView))) {
                $url->setVar('view', $view);
            }
        }

        $this->setRedirect(JRoute::_((string)$url), $message, $type);
        return true;
    }
}
