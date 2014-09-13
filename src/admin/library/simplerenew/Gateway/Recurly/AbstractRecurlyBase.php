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
    protected $client = null;

    /**
     * @var string
     */
    protected $currency = 'USD';

    protected $mode = null;

    public function __construct(Configuration $config = null)
    {
        parent::__construct($config);

        // Initialise the native Recurly API
        if ($apiKey = $this->getCfg('Apikey')) {
            $this->client = new \Recurly_Client($apiKey);
        }
        $this->currency = $this->getCfg('currency', 'USD');
    }

    /**
     * Get the desired currency amount from a Recurly currency object
     *
     * @param \Recurly_CurrencyList $amounts
     * @param string                $currency
     *
     * @return float
     */
    protected function getCurrency(\Recurly_CurrencyList $amounts, $currency = null)
    {
        $currency = $currency ? : $this->currency;

        if (isset($amounts[$currency])) {
            $amount = $amounts[$currency]->amount_in_cents / 100;
            return $amount;
        }

        return 0.0;
    }

    /**
     * Convenience method for retrieving gateway config items
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getCfg($key, $default = null)
    {
        if ($this->mode === null) {
            $this->mode = $this->gatewayConfig->get('mode', 'test');
        }

        $key     = strtolower($key);
        $modeKey = $this->mode . ucfirst($key);

        $default = $this->gatewayConfig->get($key, $default);
        return $this->gatewayConfig->get($modeKey, $default);
    }

    /**
     * Determine whether the current configuration is usable/valid
     *
     * @return bool
     */
    public function validConfiguration()
    {
        if (
            $this->client instanceof \Recurly_Client
            && ($this->client->apiKey() != '')
        ) {

            $url = sprintf($this->client->baseUri() . '/accounts');
            $ch  = curl_init($url);
            curl_setopt_array(
                $ch,
                array(
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_MAXREDIRS      => 1,
                    CURLOPT_HEADER         => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERPWD        => $this->getCfg('Apikey') . ': '
                )
            );

            curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return ($info['http_code'] == 200);
        }

        return false;
    }
}
