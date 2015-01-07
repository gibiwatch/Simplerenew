<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewViewWelcome extends SimplerenewViewAdmin
{
    protected $statusImage = array(
        'com_simplerenew/error.png',
        'com_simplerenew/ok.png'
    );

    public function display($tpl = null)
    {
        $this->setToolBar();
        parent::display($tpl);
    }

    protected function setToolbar($addDivider = false)
    {
        $this->setTitle();
        SimplerenewHelper::addSubmenu('dashboard');

        parent::setToolBar($addDivider);
    }

    /**
     * Display a standard status area with instructions
     *
     * @param bool   $status
     * @param string $langText
     * @param string $link
     * @param mixed  $attribs
     *
     * @return string
     */
    protected function renderStep($status, $langText, $link, $attribs = null)
    {
        $status = (int)(bool)$status;
        $html   = array();

        $html[] = JHtml::_('image', $this->statusImage[$status], '', null, true);
        if ($status) {
            $html[] = '<p class="ost-step ost-ok">';
            $html[] = JText::_('COM_SIMPLERENEW_WELCOME_' . $langText . '_OK');
            $html[] = '</p>';

        } else {
            $html[] = '<p class="ost-step ost-error">';
            $html[] = JText::_('COM_SIMPLERENEW_WELCOME_' . $langText . '_FIX');
            $html[] = '<br/>';

            $html[] = JHtml::_(
                'link',
                $link,
                '<span class="icon-plus"></span>' . JText::_('COM_SIMPLERENEW_WELCOME_' . $langText . '_LINKTEXT'),
                $attribs
            );
            $html[] = '</p>';
        }
        return join("\n", $html);
    }
}
