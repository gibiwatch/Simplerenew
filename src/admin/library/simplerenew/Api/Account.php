<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Gateway\AccountInterface;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Account
 * @package Simplerenew\Api
 *
 * @property-read User $user
 */
class Account extends AbstractApiBase
{
    const STATUS_ACTIVE  = 1;
    const STATUS_CLOSED  = 0;
    const STATUS_UNKNOWN = -1;

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
     * @var User
     */
    protected $user = null;

    /**
     * @var AccountInterface
     */
    private $imp = null;

    /**
     * @var string A sprintf template for generating account codes based on User ID
     */
    private $codeMask = '%s';

    public function __construct(AccountInterface $imp, array $config = array())
    {
        $this->imp = $imp;

        if (!empty($config['codeMask'])) {
            $this->setCodeMask($config['codeMask']);
        }
    }

    /**
     * Load subscription account information for the selected user
     *
     * @param User $user
     *
     * @return $this
     * @throws \Simplerenew\Exception
     */
    public function load(User $user)
    {
        $keys        = array_keys($this->getProperties());
        $accountCode = $this->getAccountCode($user);
        $newValues   = $this->imp->load($accountCode, $keys);

        $this->user = $user;
        $this->setProperties($newValues);

        return $this;
    }

    public function getCodeMask($mask = null)
    {
        if ($mask === null) {
            return $this->codeMask;
        }

        switch (substr_count($mask, '%s') == 1) {
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

        return $mask;
    }

    public function setCodeMask($mask)
    {
        $oldMask        = $this->codeMask;
        $this->codeMask = $this->getCodeMask($mask);
        return $oldMask;
    }

    /**
     * Get the account code for the selected user.
     * Defaults to currently loaded user if there is one.
     *
     * @param User   $user
     * @param string $mask
     *
     * @return null|string
     */
    public function getAccountCode(User $user = null, $mask = null)
    {
        if (!$user) {
            if ($this->code) {
                return $this->code;
            }
            $user = $this->user;
        }

        if ($user->id > 0) {
            return sprintf($this->getCodeMask($mask), $user->id);
        }

        return null;
    }
}
