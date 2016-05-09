<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

class SimplerenewApplicationCli extends JApplicationCli
{
    public function __construct(JInputCli $input = null, Registry $config = null, JEventDispatcher $dispatcher = null)
    {
        parent::__construct($input, $config, $dispatcher);

        if (!defined('SIMPLERENEW_CLI_TASK')) {
            define('SIMPLERENEW_CLI_TASK', JText::sprintf('COM_SIMPLERENEW_CLI_TASK', date('Y-m-d H:i:s')));
        }
        $this->heading(SIMPLERENEW_CLI_TASK);
    }

    /**
     * We're overriding so standard IO can be used for logging purposes.
     * Joomla uses fwrite() for output and this could be a problem when
     * STDOUT is not properly defined
     *
     * @param string $text
     * @param bool   $nl
     *
     * @return $this
     */
    public function out($text = '', $nl = true)
    {
        echo $text . ($nl ? "\n" : null);

        return $this;
    }

    /**
     * Output a message prefixed with a timestamp
     *
     * @param string $message
     * @param float  $interval
     *
     * @return $this
     */
    public function timestamp($message = null, $interval = null)
    {
        if (is_numeric($interval)) {
            $hours   = intval($interval / 60 / 60);
            $minutes = intval($interval / 60) - ($hours * 60);
            $seconds = intval($interval) - ($hours * 60 * 60) - ($minutes * 60);
            $msec    = number_format(fmod($interval, (int)$interval), 2);
            $msec    = substr($msec, strpos($msec, '.'));

            $elapsed = ($hours ? sprintf('%02s:', $hours) : '')
                . ($minutes ? sprintf('%02s:', $minutes) : '')
                . sprintf('%02s', $seconds) . $msec;
        }

        if ($message) {
            if (!empty($elapsed)) {
                $message = "[{$elapsed}] " . $message;
            }
            $this->out(date('H:i:s') . ' ' . $message);
        } else {
            $this->out();
        }

        return $this;
    }

    /**
     * Output a standard header/banner
     *
     * @param string $message
     * @param int    $width
     */
    public function heading($message, $width = 45)
    {
        $this->out()
            ->out(str_repeat('-', $width))
            ->out('|' . str_pad($message, $width - 2, ' ', STR_PAD_BOTH) . '|')
            ->out(str_repeat('-', $width));
    }
}
