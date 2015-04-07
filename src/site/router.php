<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

function SimplerenewBuildRoute(array &$query)
{
    $segments = array();

    $app = JFactory::getApplication();
    if (empty($query['Itemid'])) {
        $menuItem = $app->getMenu()->getActive();
    } else {
        $menuItem = $app->getMenu()->getItem($query['Itemid']);
    }
    $menuView   = isset($menuItem->query['view']) ? $menuItem->query['view'] : null;
    $menuLayout = isset($menuItem->query['layout']) ? $menuItem->query['layout'] : null;

    if (isset($query['view'])) {
        $view = $query['view'];
        if ($view != $menuView) {
            $segments[] = $view;
        }
        unset($query['view']);
    }

    if (isset($query['layout'])) {
        $layout = $query['layout'];
        if ($layout != $menuLayout) {
            $segments[] = $query['layout'];
        }
        unset($query['layout']);
    }

    return $segments;
}

function SimplerenewParseRoute($segments)
{
    $vars = array();

    if ($menuItem = JFactory::getApplication()->getMenu()->getActive()) {
        $vars['option'] = 'com_simplerenew';
        $vars['view']   = $menuItem->query['view'];
        if (isset($menuItem->query['layout'])) {
            $vars['layout'] = $menuItem->query['layout'];
        }
    }

    return $vars;
}
