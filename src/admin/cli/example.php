<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

$options = (getopt('', array('log::')));

if (isset($options['log'])) {
    $logFile = $options['log'] ?: substr($argv[0], 0, strrpos($argv[0], '.')) . '.log';
    if (is_file($logFile) && filesize($logFile) > (1024 * 1024)) {
        @unlink($logFile);
    }
    ob_start();
}

try {
    require_once 'bootstrap.php';
    define('SIMPLERENEW_CLI_TASK', 'SR Example CLI - ' . date('Y-m-d H:i:s'));

} catch (Exception $e) {
    echo "\n*** " . get_class($e) . ': ' . $e->getMessage() . "\n";

    if (!empty($logFile)) {
        $buffer = ob_get_contents();
        file_put_contents($logFile, $buffer, FILE_APPEND);
    }
    die;
}

class SimplerenewCliExample extends SimplerenewApplicationCli
{
    public function doExecute()
    {
        $this->timestamp('This is an example of a Simple Renew CLI script');
    }
}

SimplerenewApplicationCli::getInstance('SimplerenewCliExample')->execute();

if (!empty($logFile)) {
    $buffer = ob_get_contents();
    file_put_contents($logFile, $buffer, FILE_APPEND);
}
