<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Container;
use Simplerenew\Exception;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\Primitive\Address;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Account
 *
 * @package Simplerenew\Api
 *
 * @property-read User    $user
 * @property-read Address $address
 */
class Account extends AbstractApiBase
{
    // Status bit masks
    const STATUS_ACTIVE  = 1;
    const STATUS_CLOSED  = 2;
    const STATUS_UNKNOWN = 0;

    /**
     * @var string
     */
    public $code = null;

    /**
     * @var int
     */
    public $status = null;

    /**
     * @var string
     */
    public $username = null;

    /**
     * @var string
     */
    public $email = null;

    /**
     * @var string
     */
    public $firstname = null;

    /**
     * @var string
     */
    public $lastname = null;

    /**
     * @var string
     */
    public $company = null;

    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var AccountInterface
     */
    protected $imp = null;

    /**
     * @var string
     */
    private $codeMask = '%s';

    public function __construct(Container $container, AccountInterface $imp, Address $address = null)
    {
        parent::__construct($container);

        $config = $container->configuration;
        $this->setCodeMask($config->get('account.config.codeMask', $this->codeMask));

        $this->imp     = $imp;
        $this->address = $address ?: new Address();
    }

    /**
     * Load account information for the selected user
     *
     * @param User $user
     *
     * @return Account
     * @throws Exception
     */
    public function load(User $user)
    {
        $this->clearProperties();
        $this->address->clearProperties();

        $this->user = $user;
        $this->code = $this->getAccountCode($user->id);

        $this->imp->load($this);
        return $this;
    }

    /**
     * @param bool $create Allow account creation
     *
     * @return Account
     * @throws Exception
     */
    public function save($create = true)
    {
        if (!$this->user || empty($this->user->id)) {
            throw new Exception('No user specified for account');
        }

        $isNew = empty($this->code);
        if ($isNew && !$create) {
            throw new Exception('Creating new account is not permitted - ' . $this->user->username);
        }

        $this->setProperties(
            array(
                'code'      => $this->code ?: $this->getAccountCode($this->user->id),
                'username'  => $this->username ?: $this->user->username,
                'email'     => $this->email ?: $this->user->email,
                'firstname' => $this->firstname ?: $this->user->firstname,
                'lastname'  => $this->lastname ?: $this->user->lastname
            )
        );

        $this->imp->save($this, $isNew);
        return $this;
    }

    /**
     * Close an account.
     *
     * @return Account
     */
    public function close()
    {
        $this->imp->close($this);
        return $this;
    }

    /**
     * Reopen a closed account, or leave account open
     *
     * @return Account
     */
    public function reopen()
    {
        $this->imp->reopen($this);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCodeMask()
    {
        return $this->codeMask;
    }

    /**
     * A code mask can be used to convert a user ID into an account code for the
     * subscription manager. The mask should contain one %s token for use in
     * sprintf(). The default is to use the plain User ID as the account code.
     *
     * @param $mask
     *
     * @return string
     */
    public function setCodeMask($mask)
    {
        $oldMask = $this->codeMask;

        switch (substr_count($mask, '%s')) {
            case 1:
                // Just right!
                break;

            case 0:
                // Need at least one
                if (!empty($mask)) {
                    $mask .= '_';
                }
                $mask .= '%s';
                break;

            default:
                // Too many!
                $start = strpos($mask, '%s') + 2;
                $mask  = substr($mask, 0, $start);
                break;
        }
        $this->codeMask = $mask;

        return $oldMask;
    }

    /**
     * Get the account code for the selected user ID.
     * Defaults to currently loaded user if there is one.
     *
     * @param int $id
     *
     * @return null|string
     */
    public function getAccountCode($id = null)
    {
        if (!$id) {
            if ($this->code) {
                return $this->code;
            }
            $id = empty($this->user->id) ? null : $this->user->id;
        }

        if ($id > 0) {
            return sprintf($this->codeMask, $id);
        }

        return null;
    }

    /**
     * Get the user ID from an account code
     *
     * @param string $accountCode
     *
     * @return int
     */
    public function getUserId($accountCode = null)
    {
        if (empty($accountCode)) {
            $accountCode = $this->code;
        }

        $static = explode('%s', $this->getCodeMask());
        return str_replace($static, '', $accountCode);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Account
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}
