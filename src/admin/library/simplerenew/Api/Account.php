<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Gateway\GatewayBase;

defined('_JEXEC') or die();

class Account extends ApiBase
{
    public $code = null;
    public $status = null;
    public $username = null;
    public $email = null;
    public $firstname = null;
    public $lastname = null;
    public $company = null;
    public $address = null;

    public function __construct(GatewayBase $imp)
    {
        if (!$imp instanceof AccountInterface) {
            throw new Exception('Mismatched Gateway Object - ' . get_class($imp));
        }
        parent::__construct($imp);
    }
}
