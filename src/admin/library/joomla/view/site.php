<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class SimplerenewViewSite extends JViewLegacy
{
    /**
     * @var int
     */
    protected $step = 1;

    /**
     * Display an incrementing step header. Each subsequent
     * use adds one to the step number
     *
     * @param $text
     *
     * @return string
     */
    protected function stepHeading($text)
    {
        $step = JText::sprintf('COM_SIMPLERENEW_HEADING_STEP', $this->step++);

        $html = '<h3><span>' . $step . '</span>' . $text . '</h3>';

        return $html;
    }

    /**
     * @return JRegistry
     */
    protected function getParams()
    {
        if ($params = $this->get('Params')) {
            return $params;
        }

        return new JRegistry();
    }

    /**
     * For use on form pages that might contain sensitive information. Redirect
     * to the SSL version of the page if necessary.
     */
    protected function enforceSSL()
    {
        $uri = JURI::getInstance();

        $isSSL  = ($uri->getScheme() == 'https');
        $useSSL = $this->getParams()->get('advanced.useSSL', true);

        if (!$isSSL && $useSSL) {
            $uri->setScheme('https');
            SimplerenewFactory::getApplication()->redirect($uri->toString());
        }
    }
}
