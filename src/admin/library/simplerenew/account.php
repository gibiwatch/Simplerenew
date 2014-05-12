<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

/**
 * Class Account
 * @package Simplerenew
 *
 */
class Account extends Base
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
     * @var User
     */
    protected $user = null;

    protected $subscriptions = null;

    public function load($id=null)
    {
        $this->user = (new User())->load($id);
    }

    public function create()
    {

    }

    public function update()
    {

    }

    public function close()
    {

    }

    public function open()
    {

    }

    public function addSubscription()
    {

    }
}
