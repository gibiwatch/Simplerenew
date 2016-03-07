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
    $router   = new SimplerenewRouter();
    $segments = $router->build($query);

    return $segments;
}

function SimplerenewParseRoute($segments)
{
    $router = new SimplerenewRouter();
    $vars   = $router->parse($segments);

    return $vars;
}

class SimplerenewRouter //extends JComponentRouterBase
{
    /**
     * Build method for URLs
     * This method is meant to transform the query parameters into a more human
     * readable form. It is only executed when SEF mode is switched on.
     *
     * @param   array &$query An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
    public function build(&$query)
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
                switch ($view) {
                    case 'invoice':
                        if (!empty($query['format'])) {
                            unset($query['format']);
                        }
                        if (!empty($query['number'])) {
                            $segments[] = $query['number'];
                            unset($query['number']);
                        }
                        break;

                    default:
                        $segments[] = $view;
                        break;
                }
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

    /**
     * Parse method for URLs
     * This method is meant to transform the human readable URL back into
     * query parameters. It is only executed when SEF mode is switched on.
     *
     * @param   array $segments The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse($segments)
    {
        $vars = array();

        if ($menuItem = JFactory::getApplication()->getMenu()->getActive()) {
            $vars['option'] = 'com_simplerenew';
            $vars['view']   = $menuItem->query['view'];
            if (isset($menuItem->query['layout'])) {
                $vars['layout'] = $menuItem->query['layout'];
            } elseif (!empty($segments[0])) {
                switch ($vars['view']) {
                    case 'invoices':
                        $vars['view']   = 'invoice';
                        $vars['format'] = 'raw';
                        $vars['number']  = (int)$segments[0];
                        break;

                    default:
                        $vars['layout'] = $segments[0];
                        break;
                }
            }
        }

        return $vars;
    }
}
