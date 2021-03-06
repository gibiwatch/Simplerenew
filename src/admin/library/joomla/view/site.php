<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

abstract class SimplerenewViewSite extends SimplerenewView
{
    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var int
     */
    protected $step = 1;

    /**
     * Display an incrementing step header. Each subsequent
     * use adds one to the step number
     *
     * @param string $text
     * @param bool   $showStep
     *
     * @return string
     */
    protected function stepHeading($text, $showStep = true)
    {
        if ($showStep) {
            $step = '<span>'
                . JText::sprintf('COM_SIMPLERENEW_HEADING_STEP', $this->step++)
                . '</span>';
        } else {
            $step = '';
        }

        $html = '<h3>' . $step . $text . '</h3>';

        return $html;
    }

    /**
     * @return Registry
     */
    protected function getParams()
    {
        if ($this->params === null) {
            if (!($this->params = $this->get('Params'))) {
                $this->params = new Registry();
            }
        }

        return $this->params;
    }

    /**
     * Get the page heading from the menu definition if set
     *
     * @param string $default
     * @param bool   $translate
     *
     * @return string
     */
    protected function getHeading($default = null, $translate = true)
    {
        $params = $this->getParams();

        if ($params->get('show_page_heading')) {
            $heading = $params->get('page_heading');
        } else {
            $heading = $translate ? JText::_($default) : $default;
        }
        return $heading;
    }

    /**
     * Append page class suffix if specified
     *
     * @param string $base
     *
     * @return string
     */
    protected function getPageClass($base = '')
    {
        $suffix = $this->getParams()->get('pageclass_sfx');
        return trim($base . ' ' . $suffix);
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
