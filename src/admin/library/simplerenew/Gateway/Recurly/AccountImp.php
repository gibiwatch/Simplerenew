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
                'closed' => Account::STATUS_CLOSED,
                '::'     => Account::STATUS_UNKNOWN
            )
        )
    );

    /**
     * @param string $accountCode
     * @param array  $keys
     *
     * @return array
     * @throws Exception
     */
    public function load($accountCode, array $keys)
    {
        try {
            $result = \Recurly_Account::get($accountCode, $this->client);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $this->map($result, $keys, $this->fieldMap);
    }

    /**
     * @param Account $parent
     * @param bool    $isNew
     *
     * @return array
     * @throws Exception
     */
    public function save(Account $parent, $isNew)
    {
        try {
            \Recurly_Client::$apiKey = $this->client->apiKey();
            $account                 = new \Recurly_Account();

            $account->account_code = $parent->code;
            $account->username     = $parent->username;
            $account->email        = $parent->email;
            $account->first_name   = $parent->firstname;
            $account->last_name    = $parent->lastname;
            $account->company_name = $parent->company;

            if ($isNew) {
                $account->create();
            } else {
                $account->update();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $keys = array_keys($parent->getProperties());
        return $this->map($account, $keys, $this->fieldMap);
    }
}
