<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla;

use JLog;
use Simplerenew\AbstractLogger;
use Simplerenew\Primitive\AbstractLogEntry;
use SimplerenewFactory;

defined('_JEXEC') or die();

class Logger extends AbstractLogger
{
    protected static $debugId = null;

    protected $debugLevels = array(
        self::DEBUG_INFO  => JLog::INFO,
        self::DEBUG_WARN  => JLog::WARNING,
        self::DEBUG_ERROR => JLog::ERROR
    );

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
        $query  = $db->getQuery(true)
            ->delete('#__simplerenew_push_log')
            ->where('DATE(logtime) < ' . $db->quote($cutoff->format('Y-m-d')));

        $db->setQuery($query)->execute();
    }

    /**
     * @param string $message
     * @param int    $level
     *
     * @return void
     */
    protected function debugWrite($message, $level = self::DEBUG_INFO)
    {
        if (self::$debugId === null) {
            self::$debugId = 'SR' . substr(md5(microtime(true)), -4);
            JLog::addLogger(array('text_file' => 'simplerenew.log.php'), JLog::ALL, array(self::$debugId));
        }

        if (isset($this->debugLevels[$level])) {
            $level = $this->debugLevels[$level];
        } else {
            $level = $this->debugLevels[JLog::INFO];
        }
        JLog::add($message, $this->debugLevels[$level], self::$debugId);
    }
}
