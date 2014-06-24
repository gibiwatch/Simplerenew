<?php
/**
 * @package   test_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Tests\Simplerenew\User\Adapter;

use \Simplerenew;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the setAdapter method
     */
    public function testSetAdapter()
    {
        $adapter = new Simplerenew\User\Adapter\Joomla;
        $user = new Simplerenew\User\User($adapter);

        $this->assertEquals($adapter, \PHPUnit_Framework_Assert::readAttribute($user, 'adapter'));
    }
}
