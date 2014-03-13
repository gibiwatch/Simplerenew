<?php

class AccountTest extends \PHPUnit_Framework_TestCase
{
    public function testDummyMethod()
    {
        $acct = new Simplerenew\Account();

        $expected = true;
        $this->assertEquals($expected, $acct->dummyMethod());
    }

    public function testJunk()
    {
        $expected = preg_match('#src/admin#', SIMPLERENEW_ADMIN);
        $this->assertEquals($expected, true);
    }
}
