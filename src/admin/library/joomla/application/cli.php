<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewApplicationCli extends JApplicationCli
{
    public function __construct(JInputCli $input = null, JRegistry $config = null, JDispatcher $dispatcher = null)
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
     *
     * @return $this
     */
    public function timestamp($message = null)
    {
        if ($message) {
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
