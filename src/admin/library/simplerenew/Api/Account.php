<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Gateway\AccountInterface;
use Simplerenew\User\User;

defined('_JEXEC') or die();

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
     * @var null
     */
    public $address = null;

    /**
     * @var AccountInterface
     */
    protected $imp = null;

    /**
     * @var string A sprintf template for generating account codes based on User ID
     */
    protected $codeMask = null;

    /**
     * @var User
     */
    protected $user = null;

    public function __construct(Configuration $config, AccountInterface $imp)
    {
        parent::__construct($config);

        $this->imp = $imp;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'user':
                return $this->$name;
                break;
        }

        return parent::__get($name);
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
        $accountCode = $this->getAccountCode($user);

        $newValues = $this->getProperties(true);
        $this->imp->load($accountCode, $newValues);

        $this->user = $user;
        $this->setProperties($newValues);

        return $this;
    }

    /**
     * Get the account code for the selected user.
     * Defaults to currently loaded user if there is one.
     *
     * @param User $user
     *
     * @return null|string
     */
    public function getAccountCode(User $user = null)
    {
        if ($this->codeMask === null) {
            // Build/verify the account code mask
            $this->codeMask = $this->config->get('account.codemask');
            switch (substr_count($this->codeMask, '%s')) {
                case 0:
                    // Need at least one
                    if (!empty($this->codeMask)) {
                        $this->codeMask .= '_';
                    }
                    $this->codeMask .= '%s';
                    break;

                case 1:
                    // Just right!
                    break;

                default:
                    // Too many!
                    $start          = strpos($this->codeMask, '%s') + 2;
                    $this->codeMask = substr($this->codeMask, 0, $start);
                    break;
            }
        }

        if (!$user) {
            if ($this->code) {
                return $this->code;
            }
            $user = $this->user;
        }

        if ($user->id > 0) {
            return sprintf($this->codeMask, $user->id);
        }

        return null;
    }
}
