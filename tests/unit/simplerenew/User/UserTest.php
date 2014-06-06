<?php
/**
 * @package   test_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Tests\Simplerenew\User;

use \Simplerenew;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The Joomla User Adapter
     * @var Simplerenew\User\Adapter\Joomla
     */
    protected $joomlaUserAdapter;

    /**
     * The Mock User
     * @var Tests\Mock\User
     */
    protected $mockUser;

    /**
     * Method to setup the test
     */
    public function setUp()
    {
        $this->joomlaUserAdapter = new Simplerenew\User\Adapter\Joomla;
        $this->mockUser          = new \Tests\Mock\User;

        $this->createJoomlaUserForTests();
    }

    /**
     * This will insert the dummy user to the database
     */
    protected function createJoomlaUserForTests()
    {
        jimport('joomla.user.helper');
        jimport('joomla.application.component.helper');

        $joomlaUser = new \JUser;
        $mockUserData = $this->mockUser->getData();

        // Generate the new password hash
        $salt     = \JUserHelper::genRandomPassword(32);
        $crypted  = \JUserHelper::getCryptedPassword($mockUserData['password'], $salt);
        $mockUserData['password'] = $crypted . ':' . $salt;

        // Get the default user group
        $config = \JComponentHelper::getParams('com_users');
        $mockUserData['group'] = array($config->get('new_usertype', 2));

        if (!$joomlaUser->bind($mockUserData)) {
            return \JError::raiseWarning(1, $joomlaUser->getErrors());
        }

        if (!$joomlaUser->save()) {
            return \JError::raiseWarning(1, $joomlaUser->getErrors());
        }

        $this->mockUser->id = $joomlaUser->id;
    }

    /**
     * This will drop the user data from the databse
     */
    protected function deleteJoomlaUserForTests()
    {
        $joomlaUser = new \JUser($this->mockUser->id);
        $joomlaUser->delete();
    }

    /**
     * Test the load method
     */
    public function testLoad()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->load($this->mockUser->id);

        $this->assertEquals($this->mockUser->username, $user->username);
    }

    /**
     * Test the loadByUsername method
     */
    public function testLoadByUsername()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->loadByUsername($this->mockUser->username);

        $this->assertEquals($this->mockUser->id, $user->id);
    }

    /**
     * This method will run after all tests has being executed
     */
    public function tearDown()
    {
        $this->deleteJoomlaUserForTests();

        unset($this->user);
        unset($this->joomlaUserAdapter);
        unset($this->mockUser);
    }
}
