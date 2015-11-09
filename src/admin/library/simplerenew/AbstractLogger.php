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
    const DEBUG_INFO  = 1;
    const DEBUG_WARN  = 2;
    const DEBUG_ERROR = 4;

    /**
     * @var float
     */
    protected $debugStart = null;

    /**
     * @var float
     */
    protected $debugLastCall = null;

    /**
     * @var float
     */
    protected $debugLastHeading = null;

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

    /**
     * @param string $message
     * @param int    $level
     *
     * @return void
     */
    abstract protected function debugWrite($message, $level = self::DEBUG_INFO);

    /**
     * Add a debug log entry including elapsed time
     *
     * @param string $message
     * @param int    $level
     * @param bool   $heading
     *
     * @return void
     */
    public function debug($message, $level = self::DEBUG_INFO, $heading = false)
    {
        if ($this->debugStart === null) {
            $this->debugStart    = microtime(true);
            $this->debugLastCall = $this->debugStart;
        }

        $now     = microtime(true);
        $elapsed = $now - $this->debugLastCall;

        $this->debugLastCall = $now;
        if ($heading) {
            if ($this->debugLastHeading !== null) {
                $message .= ' (' . number_format($now - $this->debugLastHeading, 4) . ')';
            }
            $width   = max(40, strlen($message) + 6);
            $message = str_pad(' ' . $message . ' ', $width, '*', STR_PAD_BOTH);

            $this->debugLastHeading = $now;

        } else {
            $message = number_format($elapsed, 4) . ' ' . $message;
        }
        $this->debugWrite($message, $level);
    }
}
