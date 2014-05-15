<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Configuration;
use Simplerenew\Exception;
use Simplerenew\Gateway\AbstractGatewayBase;

defined('_JEXEC') or die();

require_once __DIR__ . '/api/autoloader.php';

abstract class AbstractRecurlyBase extends AbstractGatewayBase
{
    /**
     * @var object
     */
    protected static $settings = null;

    public function __construct(Configuration $config)
    {
        if (self::$settings === null) {
            self::$settings = $config->get('gateway');

            if (!empty(self::$settings->mode)) {
                $mode = self::$settings->mode;
                if (!empty(self::$settings->$mode->apikey)) {
                    $apikey = self::$settings->$mode->apikey;
                }
            }

            if (!empty($apikey)) {
                \Recurly_Client::$apiKey = $apikey;
            } else {
                throw new Exception('Recurly API requires an api key');
            }
        }
    }
}
