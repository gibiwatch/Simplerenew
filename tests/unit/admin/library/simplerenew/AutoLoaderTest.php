<?php

class AutoLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AutoLoaderMock
     */
    protected $loader = null;

    protected function setUp()
    {
        require_once 'AutoLoaderMock.php';
        AutoLoaderMock::setFiles(array(
            '/vendor/foo.bar/src/ClassName.php',
            '/vendor/foo.bar/src/DoomClassName.php',
            '/vendor/foo.bar/tests/ClassNameTest.php',
            '/vendor/foo.bardoom/src/ClassName.php',
            '/vendor/foo.bar.baz.dib/src/ClassName.php',
            '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php',
            '/local/camels/class.php',
            '/local/camels/foo/bar.php',
            '/local/camels/foobar.php',
            '/local2/camels/class.php',
            '/local2/camels/foo/bar.php'
        ));

        $this->loader = AutoLoaderMock::getInstance();

        AutoLoaderMock::register('Foo\Bar', '/vendor/foo.bar/src');
        AutoLoaderMock::register('Foo\Bar', '/vendor/foo.bar/tests');
        AutoLoaderMock::register('Foo\BarDoom', '/vendor/foo.bardoom/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib', '/vendor/foo.bar.baz.dib/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib\Zim\Gir', '/vendor/foo.bar.baz.dib.zim.gir/src');

        AutoLoaderMock::registerCamelBase('Camel', '/local/camels');
        AutoLoaderMock::registerCamelBase('Hump', '/local2/camels');
    }

    public function testClassExisting()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassName');
        $expect = '/vendor/foo.bar/src/ClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassNameTest');
        $expect = '/vendor/foo.bar/tests/ClassNameTest.php';
        $this->assertSame($expect, $actual);
    }

    public function testClassMissing()
    {
        $actual = $this->loader->mockLoadClass('No_Vendor\No_Package\NoClass');
        $this->assertFalse($actual);
    }

    public function testClassDeep()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');
        $expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }

    public function testClassConfusion()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\DoomClassName');
        $expect = '/vendor/foo.bar/src/DoomClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\BarDoom\ClassName');
        $expect = '/vendor/foo.bardoom/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }

    public function testCamelExisting()
    {
        $actual = $this->loader->mockLoadCamelClass('CamelClass');
        $expect = '/local/camels/class.php';
        $this->assertSame($expect, $actual);
    }

    public function testCamelMissing()
    {
        $actual = $this->loader->mockLoadCamelClass('NoSuchClass');
        $this->assertFalse($actual);
    }

    public function testCamelDeep()
    {
        $actual = $this->loader->mockLoadCamelClass('CamelFooBar');
        $expect = '/local/camels/foo/bar.php';
        $this->assertSame($expect, $actual);
    }

    public function testCamelConfusion()
    {
        $actual = $this->loader->mockLoadCamelClass('CamelFooBar');
        $expect = '/local/camels/foo/bar.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadCamelClass('CamelFoobar');
        $expect = '/local/camels/foobar.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadCamelClass('HumpFooBar');
        $expect = '/local2/camels/foo/bar.php';
        $this->assertSame($expect, $actual);
    }

}
