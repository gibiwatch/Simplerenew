<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

class JFormFieldThemes extends JFormFieldList
{
    public function getOptions()
    {
        $options = array(
            JHtml::_('select.option', 'none', JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_NONE')),
            JHtml::_('select.option', 'default.css', JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_DEFAULT'))
        );

        $path = JPATH_SITE . '/media/com_simplerenew/css/themes';

        $styles = JFolder::files($path, '(?<!default)\.css');
        sort($styles);
        foreach ($styles as $file) {
            $options[] = JHtml::_(
                'select.option',
                $file,
                JText::_('COM_SIMPLERENEW_OPTION_STYLESHEET_' . strtoupper(basename($file, '.css')))
            );
        }

        return $options;
    }
}
