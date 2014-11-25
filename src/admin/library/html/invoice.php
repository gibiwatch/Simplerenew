<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class JHtmlInvoice
{
    public static function pdflink($number)
    {
        $app = SimplerenewFactory::getApplication();

        $query = array(
            'option' => 'com_simplerenew',
            'view' => 'invoice',
            'format' => 'raw',
            'number' => $number
        );
        if ($itemid = $app->input->getInt('Itemid')) {
            $query['Itemid'] = $itemid;
        }

        $link = JRoute::_('index.php?' . http_build_query($query));
        return JHtml::link($link, $number);
    }
}
