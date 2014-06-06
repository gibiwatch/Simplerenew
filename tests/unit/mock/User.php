<?php
/**
 * @package   test_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Tests\Mock;

class User extends Base
{
    public $id;

    public $firstname;

    public $lastname;

    public $name;

    public $email;

    public $password;

    public $username;

    public function __construct()
    {
        $uid = uniqid();

        $this->id = 0;
        $this->firstname = 'Mock';
        $this->lastname = 'User' . $uid;
        $this->name = $this->firstname . ' ' . $this->lastname;
        $this->email    = 'user' . $uid . '@immock.me';
        $this->username = 'mock' . $uid;
        $this->password = 'mock';
    }
}
