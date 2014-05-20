<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Exception;
use Simplerenew\Gateway\BillingInterface;

defined('_JEXEC') or die();

class BillingImp extends AbstractRecurlyBase implements BillingInterface
{
    protected $fieldMap = array(
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'region'    => 'state',
        'postal'    => 'zip',
        'ipaddress' => 'ip_address'
    );

    public function load($accountCode, array $keys)
    {
        try {
            $billing = \Recurly_BillingInfo::get($accountCode, $this->client);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $this->map($billing, $keys, $this->fieldMap);
    }
}
