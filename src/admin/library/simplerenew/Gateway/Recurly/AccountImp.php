<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Exception;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\AbstractGatewayBase;

defined('_JEXEC') or die();

class AccountImp extends AbstractGatewayBase implements AccountInterface
{
    public function __construct(array $config = array())
    {
        require_once __DIR__ . '/api/autoloader.php';

        if (empty(\Recurly_Client::$apiKey)) {
            if (!empty($config['apikey'])) {
                \Recurly_Client::$apiKey = $config['apikey'];
            } else {
                throw new Exception('Recurly API requires an api key');
            }
        }
    }

    public function load($accountCode)
    {
        try {
            $result = \Recurly_Account::get($accountCode);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        return $result;
    }
}
