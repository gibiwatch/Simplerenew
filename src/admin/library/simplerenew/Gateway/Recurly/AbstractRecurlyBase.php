<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Exception;
use Simplerenew\Gateway\AbstractGatewayBase;

defined('_JEXEC') or die();

require_once __DIR__ . '/api/autoloader.php';

abstract class AbstractRecurlyBase extends AbstractGatewayBase
{
    /**
     * @var \Recurly_Client
     */
    protected $client = null;

    public function __construct(array $config = array())
    {
        // Initialise the native Recurly API
        $mode = empty($config['mode']) ? 'test' : $config['mode'];

        if (!empty($config[$mode]['apikey'])) {
            $this->client = new \Recurly_Client($config[$mode]['apikey']);
        } else {
            throw new Exception('Recurly API requires an api key');
        }
    }
}
