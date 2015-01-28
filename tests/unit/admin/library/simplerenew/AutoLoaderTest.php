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
        ));

        $this->loader = AutoLoaderMock::getInstance();

        AutoLoaderMock::register('Foo\Bar', '/vendor/foo.bar/src');
        AutoLoaderMock::register('Foo\Bar', '/vendor/foo.bar/tests');
        AutoLoaderMock::register('Foo\BarDoom', '/vendor/foo.bardoom/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib', '/vendor/foo.bar.baz.dib/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib\Zim\Gir', '/vendor/foo.bar.baz.dib.zim.gir/src');
    }

    public function testExistingFile()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassName');
        $expect = '/vendor/foo.bar/src/ClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassNameTest');
        $expect = '/vendor/foo.bar/tests/ClassNameTest.php';
        $this->assertSame($expect, $actual);
    }

    public function testMissingFile()
    {
        $actual = $this->loader->mockLoadClass('No_Vendor\No_Package\NoClass');
        $this->assertFalse($actual);
    }

    public function testDeepFile()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');
        $expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }

    public function testConfusion()
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\DoomClassName');
        $expect = '/vendor/foo.bar/src/DoomClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\BarDoom\ClassName');
        $expect = '/vendor/foo.bardoom/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }
}
