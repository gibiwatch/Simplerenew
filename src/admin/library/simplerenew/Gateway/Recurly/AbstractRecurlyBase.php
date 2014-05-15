<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Configuration;
use Simplerenew\Gateway\AbstractGatewayBase;

defined('_JEXEC') or die();

require_once __DIR__ . '/api/autoloader.php';

abstract class AbstractRecurlyBase extends AbstractGatewayBase
{
    public function __construct(Configuration $config)
    {
        if (empty(\Recurly_Client::$apiKey)) {
            $settings = $config->get($config->get('mode'));

            if (!empty($settings->apikey)) {
                \Recurly_Client::$apiKey = $settings->apikey;
            } else {
                throw new Exception('Recurly API requires an api key');
            }
        }
    }
}
