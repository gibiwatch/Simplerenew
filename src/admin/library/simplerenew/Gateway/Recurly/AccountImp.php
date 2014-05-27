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
use Simplerenew\Primitive\Address;

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
     * @var array associative array of \Recurly_Account objects previously loaded
     */
    protected $accountsLoaded = array();

    /**
     * @param Account $parent
     *
     * @return array
     * @throws Exception
     */
    public function load(Account $parent)
    {
        $account = $this->getAccount($parent->code);
        $parent->setProperties($account, $this->fieldMap);

        if ($parent->address instanceof Address) {
            $parent->address->setProperties(
                $account->address,
                array(
                    'region' => 'state',
                    'postal' => 'zip'
                )
            );
        }
    }

    /**
     * @param Account $parent
     * @param bool    $isNew
     *
     * @return void
     * @throws Exception
     */
    public function save(Account $parent, $isNew)
    {
        $account = $this->getAccount($parent->code);

        $account->username     = $parent->username;
        $account->email        = $parent->email;
        $account->first_name   = $parent->firstname;
        $account->last_name    = $parent->lastname;
        $account->company_name = $parent->company;

        try {
            if ($isNew) {
                $account->create();
            } else {
                $account->update();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function close(Account $parent)
    {
        try {
            $account = $this->getAccount($parent->code);

            if ($account->state != 'closed') {
                $account->close();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function reopen(Account $parent)
    {
        try {
            $account = $this->getAccount($parent->code);

            if ($account->state != 'active') {
                $account->reopen();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $code
     *
     * @return \Recurly_Account
     * @throws Exception
     */
    protected function getAccount($code)
    {
        if (empty($this->accountsLoaded[$code])) {
            try {
                $this->accountsLoaded[$code] = \Recurly_Account::get($code, $this->client);

            } catch (\Recurly_NotFoundError $e) {
                $this->accountsLoaded[$code] = new \Recurly_Account($code, $this->client);

            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $this->accountsLoaded[$code];
    }
}
