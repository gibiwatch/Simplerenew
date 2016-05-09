<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Object;
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
                'active'              => Account::STATUS_ACTIVE,
                'closed'              => Account::STATUS_CLOSED,
                Object::MAP_UNDEFINED => Account::STATUS_UNKNOWN
            )
        )
    );

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Account $parent
     * @param mixed   $data
     *
     * @return void
     */
    public function bindSource(Account $parent, $data)
    {
        $parent
            ->clearProperties()
            ->setProperties($data, $this->fieldMap);

        $address = $this->getKeyValue($data, 'address');
        if ($address && $parent->address instanceof Address) {
            $parent->address
                ->clearProperties()
                ->setProperties(
                    $address,
                    array(
                        'region' => 'state',
                        'postal' => 'zip'
                    )
                );
        }
    }

    /**
     * @param Account $parent
     *
     * @return array
     * @throws Exception
     */
    public function load(Account $parent)
    {
        $account = $this->getAccount($parent->code);

        $this->bindSource($parent, $account);
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
        $isNew = false;
        try {
            $account = $this->getAccount($parent->code);

        } catch (NotFound $e) {
            $account = new \Recurly_Account($parent->code, $this->client);
            $isNew   = true;
        }

        $account->username     = $parent->username;
        $account->email        = $parent->email;
        $account->first_name   = $parent->firstname;
        $account->last_name    = $parent->lastname;
        $account->company_name = $parent->company;

        $account->address           = new \Recurly_Address();
        $account->address->address1 = $parent->address->address1;
        $account->address->address2 = $parent->address->address2;
        $account->address->city     = $parent->address->city;
        $account->address->state    = $parent->address->region;
        $account->address->zip      = $parent->address->postal;
        $account->address->country  = $parent->address->country;

        try {
            if ($isNew) {
                $account->create();
            } else {
                $account->update();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
        $this->load($parent);
    }

    /**
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function close(Account $parent)
    {
        $account = $this->getAccount($parent->code);
        try {
            if ($account->state != 'closed') {
                $account->close();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $this->load($parent);
    }

    /**
     * @param Account $parent
     *
     * @return void
     * @throws Exception
     */
    public function reopen(Account $parent)
    {
        $account = $this->getAccount($parent->code);
        try {
            if ($account->state != 'active') {
                $account->reopen();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $this->load($parent);
    }

    /**
     * @param $code
     *
     * @return \Recurly_Account
     * @throws Exception
     */
    protected function getAccount($code)
    {
        try {
            $account = \Recurly_Account::get($code, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $account;
    }
}
