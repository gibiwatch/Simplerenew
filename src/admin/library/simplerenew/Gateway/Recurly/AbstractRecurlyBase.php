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

    /**
     * @var string
     */
    protected $currency = 'USD';

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        // Initialise the native Recurly API
        $mode = empty($this->gatewayConfig['mode']) ? 'test' : $this->gatewayConfig['mode'];

        if (!empty($this->gatewayConfig[$mode . 'Apikey'])) {
            $this->client = new \Recurly_Client($this->gatewayConfig[$mode . 'Apikey']);
        } else {
            throw new Exception('Recurly API requires an api key');
        }

        $this->currency = empty($this->gatewayConfig['currency']) ? 'USD' : $this->gatewayConfig['currency'];
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
}
