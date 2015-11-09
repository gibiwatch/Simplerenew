<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
     *    - Uses current 'option', 'view', 'layout' URL variables
     *    - Goes to site default page
     *
     * @param array|string $message The message to queue up
     * @param string       $type    message|notice|error
     * @param string       $return  (optional) base64 encoded url for redirect
     *
     * @return void
     */
    protected function callerReturn($message = null, $type = null, $return = null)
    {
        $app = SimplerenewFactory::getApplication();

        $url = $return ?: $app->input->getBase64('return');
        if ($url) {
            $url = base64_decode($url);

        } else {
            $url = new JURI('index.php');

            if ($itemid = $app->input->getInt('Itemid')) {
                $menu = $app->getMenu()->getItem($itemid);

                $url->setVar('Itemid', $itemid);

            } elseif ($option = $app->input->getCmd('option')) {
                $url->setVar('option', $option);
            }

            if ($view = $app->input->getCmd('view')) {
                $url->setVar('view', $view);
                if ($layout = $app->input->getCmd('layout')) {
                    $url->setVar('layout', $layout);
                }
            }
        }

        if (is_array($message)) {
            $message = join('<br/>', $message);
        }
        $this->setRedirect(JRoute::_((string)$url), $message, $type);
    }
}
