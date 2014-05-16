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
     * @var \Recurly_Client
     */
    protected static $recurlyClient = null;

    public function __construct(Configuration $config)
    {
        parent::__construct($config);

        if (self::$recurlyClient === null) {
            // Initialise the native Recurly API
            $mode = $this->config->get('gateway.mode');
            $keys = $this->config->get("gateway.{$mode}");

            if (!empty($keys->apikey)) {
                self::$recurlyClient = new \Recurly_Client($keys->apikey);
            } else {
                throw new Exception('Recurly API requires an api key');
            }
        }
    }
}
