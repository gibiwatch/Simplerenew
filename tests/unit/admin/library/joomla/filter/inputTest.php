<?php

class SimplerenewFilterInputTest extends \PHPUnit_Framework_TestCase
{
    public function casesTestArrayKeys()
    {
        return array(
            'single_dim' => array(
                array(
                    'ab<>cd' => 'ef<>gh'
                ),
                array(
                    'abcd' => 'efgh'
                )
            ),
            'multi-dim' => array(
                array(
                    'ab<>cd' => array(
                        'ef<>gh' => 'ij<>kl'
                    )
                ),
                array(
                    'abcd' => array(
                        'efgh' => 'ijkl'
                    )
                )
            ),
            'non-array' => array(
                'ab<>cd',
                'abcd'
            )
        );
    }

    /**
     * @param array $input
     * @param array $expected
     *
     * @dataProvider casesTestArrayKeys
     */
    public function testArrayKeys($input, $expected)
    {
        $filter = SimplerenewFilterInput::getInstance();

        $this->assertEquals(
            $expected,
            $filter->clean($input, 'array_keys')
        );
    }
}
