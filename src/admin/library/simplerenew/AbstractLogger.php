<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Primitive\AbstractLogEntry;

defined('_JEXEC') or die();

/**
 * Class AbstractLogger
 *
 * @TODO: Review Logger classes, work towards better construction
 *
 * @package Simplerenew
 */
abstract class AbstractLogger
{
    /**
     * Add a new log entry
     *
     * @param AbstractLogEntry $entry
     *
     * @return void
     */
    public function add(AbstractLogEntry $entry)
    {
        $this->insertEntry($entry);
        $this->trimLogs();
    }

    /**
     * Insert the log entry into the database
     *
     * @param AbstractLogEntry $entry
     *
     * @return void
     */
    abstract protected function insertEntry(AbstractLogEntry $entry);

    /**
     * Remove obsolete entries from all logs
     *
     * @return void
     */
    abstract protected function trimLogs();
}
