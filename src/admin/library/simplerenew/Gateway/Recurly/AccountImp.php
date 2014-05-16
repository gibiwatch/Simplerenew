<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Exception;
use Simplerenew\Gateway\AccountInterface;

defined('_JEXEC') or die();

class AccountImp extends AbstractRecurlyBase implements AccountInterface
{
    /**
     * @var array Key = Simplerenew name, Value =  Recurly name
     */
    protected $fieldMap = array(
        'code'      => 'account_code',
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'company'   => 'company_name',
        'status'    => array(
            'state' => array(
                'active' => Account::STATUS_ACTIVE,
                'closed' => Account::STATUS_CLOSED
            )
        )
    );

    public function load($accountCode, array &$data)
    {
        try {
            $result = \Recurly_Account::get($accountCode, self::$recurlyClient);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($data as $srKey => $recurlyKey) {
            if (isset($this->fieldMap[$srKey])) {
                if (is_array($this->fieldMap[$srKey])) {
                    $values       = reset($this->fieldMap[$srKey]);
                    $field        = key($this->fieldMap[$srKey]);
                    $selected     = $result->$field;
                    $data[$srKey] = isset($values[$selected]) ? $values[$selected] : Account::STATUS_UNKNOWN;
                } else {
                    $field        = $this->fieldMap[$srKey];
                    $data[$srKey] = $result->$field;
                }
            } else {
                $data[$srKey] = $result->$srKey;
            }
        }
    }
}
