<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

class JFormFieldCustomstyles extends JFormFieldList
{
    public function getOptions()
    {
        $options = array(
            JHtml::_('select.option', '', JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_NONE')),
            JHtml::_('select.option', 'default.css', JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_DEFAULT'))
        );

        $path = JPATH_SITE . '/' . dirname(JHtml::stylesheet('com_simplerenew/themes/default.css', null, true, true));

        $styles = JFolder::files($path, '\.css$');
        sort($styles);
        foreach ($styles as $file) {
            if ($file != 'default.css') {
                $options[] = JHtml::_(
                    'select.option',
                    $file,
                    JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_' . strtoupper(basename($file, '.css')))
                );
            }
        }

        return $options;
    }
}
