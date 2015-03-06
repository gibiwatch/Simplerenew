<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla;

use Simplerenew\AbstractLogger;
use Simplerenew\Primitive\AbstractLogEntry;
use SimplerenewFactory;

defined('_JEXEC') or die();

class Logger extends AbstractLogger
{
    /**
     * Insert the log entry into the database
     *
     * @param AbstractLogEntry $entry
     *
     * @return void
     */
    protected function insertEntry(AbstractLogEntry $entry)
    {
        $db = SimplerenewFactory::getDbo();
        $db->insertObject('#__simplerenew_push_log', $entry, 'id');
    }

    /**
     * Remove obsolete entries from all logs
     *
     * @return void
     */
    protected function trimLogs()
    {
        $db = SimplerenewFactory::getDbo();

        $cutoff = new \DateTime('-1 year');
        $query = $db->getQuery(true)
            ->delete('#__simplerenew_push_log')
            ->where('DATE(logtime) < ' . $db->quote($cutoff->format('Y-m-d')));

        $db->setQuery($query)->execute();
    }
}
