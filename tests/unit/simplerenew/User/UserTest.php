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
     * Method to setup each test
     */
    public function setUp()
    {
        $this->joomlaUserAdapter = new Simplerenew\User\Adapter\Joomla;
        $this->mockUser          = new \Tests\Mock\User;

        $this->createJoomlaUserForTest();
    }

    /**
     * This method will run after each test
     */
    public function tearDown()
    {
        $this->deleteJoomlaUsers();

        unset($this->joomlaUserAdapter);
        unset($this->mockUser);
    }

    /**
     * Get the user from database by username
     *
     * @param string $username The username
     *
     * @return int The user id
     */
    protected function getUserIdByUsername($username)
    {
        $db = \JFactory::getDBO();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__users')
            ->where('username = ' . $db->quote($username));
        $db->setQuery($query);

        $id = $db->loadResult();

        return (int) $id;
    }

    /**
     * This will insert the dummy user to the database
     */
    protected function createJoomlaUserForTest()
    {
        $model = \JModelLegacy::getInstance('Registration', 'UsersModel');

        $data = array(
            'email1'    => $this->mockUser->email,
            'username'  => $this->mockUser->username,
            'name'      => trim($this->mockUser->firstname . ' ' . $this->mockUser->lastname),
            'password1' => $this->mockUser->password
        );

        if ($id = $model->register($data)) {
            $this->mockUser->id = $this->getUserIdByUsername($this->mockUser->username);

            // Activate the user
            $db = \JFactory::getDBO();
            $query = $db->getQuery(true)
                ->set('block = 0')
                ->update('#__users')
                ->where('id = ' . $db->quote($this->mockUser->id));
            $db->setQuery($query);
            $db->query();

            return;
        }

        throw new \Exception("Error saving user data", 1);
    }

    /**
     * This will delete the users from the database
     */
    protected function deleteJoomlaUsers()
    {
        $db = \JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__users') . ' WHERE email LIKE ' . $db->quote('%@immock.me');
        $db->setQuery($query);

        $db->execute();
    }

    /**
     * Test the setAdapter method
     */
    public function testSetAdapter()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);

        $this->assertEquals($this->joomlaUserAdapter, \PHPUnit_Framework_Assert::readAttribute($user, 'adapter'));
    }

    /**
     * Test the load method for a valid user
     */
    public function testLoadValid()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->load($this->mockUser->id);

        $this->assertEquals($this->mockUser->username, $user->username);
    }

    /**
     * Test the load method for a invalid user
     *
     * @expectedException Simplerenew\Exception
     */
    public function testLoadInvalid()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->load(-9999);
    }

    /**
     * Test the load method without param
     *
     * @expectedException Simplerenew\Exception
     */
    public function testLoadNoParam()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->load();
    }

    /**
     * Test the loadByUsername method for a valid user
     */
    public function testLoadByUsernameValid()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->loadByUsername($this->mockUser->username);

        $this->assertEquals($this->mockUser->id, $user->id);
    }

    /**
     * Test the loadByUsername method for a invalid user
     *
     * @expectedException Simplerenew\Exception
     */
    public function testLoadByUsernameInvalid()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->loadByUsername((string) microtime());
    }

    /**
     * Test the loadByUsername method without param
     *
     * @expectedException Simplerenew\Exception
     */
    public function testLoadByUsernameNoParam()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user = $user->loadByUsername();
    }

    /**
     * Test the convertion to string
     */
    public function testAsString()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);

        $this->assertEquals('Simplerenew\User\Adapter\Joomla', (string) $user);
    }

    /**
     * Test the user creation with id set
     *
     * @expectedException Simplerenew\Exception
     */
    public function testCreateNotEmptyId()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->id = 999999;

        $user->create();
    }

    /**
     * Test the user creation
     */
    public function testCreate()
    {
        // Create a new mock user to avoid conflict with the current one
        $mockUser = new \Tests\Mock\User;

        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->firstname = $mockUser->firstname;
        $user->lastname = $mockUser->lastname;
        $user->username = $mockUser->username;
        $user->email = $mockUser->email;
        $user->password = $mockUser->password;

        $user->create();

        $this->assertGreaterThan(0, $user->id, "The user wasn't created");
    }

    /**
     * Test the user update without id
     *
     * @expectedException Simplerenew\Exception
     */
    public function testUpdateEmptyId()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->loadByUsername($this->mockUser->username);

        $user->id = 0;

        $user->update();
    }

    /**
     * Test the user update with invalid id
     *
     * @expectedException Simplerenew\Exception
     */
    public function testUpdateInvalidId()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->loadByUsername($this->mockUser->username);

        $user->id = -99999;

        $user->update();
    }

    /**
     * Test the user update with valid data
     */
    public function testUpdate()
    {
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->loadByUsername($this->mockUser->username);

        $uid = uniqid();

        // The new data
        $firstname = 'Updated Mock';
        $lastname  = 'User' . $uid;
        $name      = $firstname . ' ' . $lastname;
        $email     = 'newemail' . $uid . '@immock.me';
        $username  = 'newusername' . $uid;
        $password  = 'mynewpassword' . $uid;

        // Update the user
        $user->firstname = $firstname;
        $user->lastname  = $lastname;
        $user->email     = $email;
        $user->username  = $username;
        $user->password  = $password;
        $user->update();

        // Get user from DB
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('name')
            ->select('email')
            ->select('username')
            ->from('#__users')
            ->where('id = ' . $db->quote($user->id));
        $db->setQuery($query);
        $userOnDB = $db->loadObject();

        $this->assertEquals($name, $userOnDB->name, "The user name wasn't updated successfully");
        $this->assertEquals($email, $userOnDB->email, "The email wasn't updated successfully");
        $this->assertEquals($username, $userOnDB->username, "The username wasn't updated successfully");
    }

    /**
     * Test the user update refreshing the session data
     */
    public function testUpdateSession()
    {
        // Log in the mock user
        $credentials = array();
        $credentials['username'] = $this->mockUser->username;
        $credentials['password'] = $this->mockUser->password;
        \JFactory::getApplication('site')->login($credentials);

        // The new data
        $uid = uniqid();
        $firstname = 'Updated Mock';
        $lastname  = 'User' . $uid;
        $name      = $firstname . ' ' . $lastname;
        $email     = 'newemail' . $uid . '@immock.me';
        $username  = 'newusername' . $uid;
        $password  = 'mynewpassword' . $uid;

        // Update the user
        $user = new Simplerenew\User\User($this->joomlaUserAdapter);
        $user->loadByUsername($this->mockUser->username);

        $user->firstname = $firstname;
        $user->lastname  = $lastname;
        $user->email     = $email;
        $user->username  = $username;
        $user->password  = $password;
        $user->update();

        // Get user from session
        $sessionUser = \JFactory::getUser();

        $this->assertEquals($name, $sessionUser->name, "The user name wasn't updated successfully on session");
        $this->assertEquals($email, $sessionUser->email, "The email wasn't updated successfully on session");
        $this->assertEquals($username, $sessionUser->username, "The username wasn't updated successfully on session");
    }
}
