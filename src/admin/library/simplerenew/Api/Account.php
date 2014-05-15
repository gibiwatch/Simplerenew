<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Gateway\AccountInterface;
use Simplerenew\User;

defined('_JEXEC') or die();

class Account extends AbstractApiBase
{
    public $code = null;
    public $status = null;
    public $username = null;
    public $email = null;
    public $firstname = null;
    public $lastname = null;
    public $company = null;
    public $address = null;

    /**
     * @var AccountInterface
     */
    protected $imp = null;

    /**
     * @var User
     */
    protected $user = null;

    public function __construct(AccountInterface $imp)
    {
        parent::__construct($imp);
    }

    public function load(User $user)
    {
        return $this->imp->load('OS_' . $user->id);
    }

}
