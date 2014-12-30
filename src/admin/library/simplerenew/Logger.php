<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Primitive\LogEntry;

defined('_JEXEC') or die();

/**
 * Class Logger
 *
 * N.B.!! - this implementation is pretty much wrong. A better system needs to be worked out
 * @TODO: Come up with a decent abstracted logging system and/or ORM interface
 *
 * @package Simplerenew
 */
abstract class Logger
{
    public static function addEntry($data)
    {
        $entry = new LogEntry($data);

        if (class_exists('JDatabase')) {
            $db = \SimplerenewFactory::getDbo();
            $db->insertObject('#__simplerenew_push_log', $entry, 'id');

            // Trim the log table
            $cutoff = new \DateTime('-1 year');
            $query = $db->getQuery(true)
                ->delete('#__simplerenew_push_log')
                ->where('DATE(logtime) < ' . $db->quote($cutoff->format('Y-m-d')));

            $db->setQuery($query)->execute();
        }
    }
}
