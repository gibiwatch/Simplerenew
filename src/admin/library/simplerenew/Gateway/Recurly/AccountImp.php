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
     * @var array Map Recurly field names to Simplerenew field names where different
     */
    protected $fieldMap = array(
        'code'      => 'account_code',
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'company'   => 'company_name'
    );

    /**
     * @var array Map Recurly state to Simplerenew status
     */
    protected $stateMap = array(
        'active' => Account::STATUS_ACTIVE,
        'closed' => Account::STATUS_CLOSED
    );

    public function load($accountCode, array &$data)
    {
        try {
            $result = \Recurly_Account::get($accountCode);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($data as $k => $v) {
            switch ($k) {
                case 'address':
                    // Unrecognized fields
                    break;

                case 'status':
                    /** @noinspection PhpUndefinedFieldInspection */
                    $state = $result->state;
                    if (isset($this->stateMap[$state])) {
                        $data[$k] = $this->stateMap[$state];
                    } else {
                        $data[$k] = Account::STATUS_UNKNOWN;
                    }
                    break;

                default:
                    if (isset($this->fieldMap[$k])) {
                        $field    = $this->fieldMap[$k];
                        $data[$k] = $result->$field;
                    } else {
                        $data[$k] = $result->$k;
                    }
            }
        }
    }
}
