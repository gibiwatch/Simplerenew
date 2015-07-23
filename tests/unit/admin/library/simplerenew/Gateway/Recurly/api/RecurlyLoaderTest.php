<?php

class RecurlyLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $allClasses = array();

    protected $exceptions = array();

    protected function setUp()
    {
        // This should load the Recurly autoloader
        class_exists('\Simplerenew\Gateway\Recurly\AbstractRecurlyBase');

        $refClass   = new ReflectionClass('RecurlyLoader');
        $exceptions = $refClass->getProperty('exceptions');
        $exceptions->setAccessible(true);
        $this->exceptions = $exceptions->getValue();

        $this->allClasses = $this->getClasses(SIMPLERENEW_LIBRARY . '/simplerenew/Gateway/Recurly/api/recurly');
    }

    protected function getClasses($path)
    {
        $classes = array();
        $files   = scandir($path);
        foreach ($files as $file) {
            if (is_dir($path . '/' . $file)) {
                if (!in_array($file, array('.', '..'))) {
                    $classes = array_merge($classes, $this->getClasses($path . '/' . $file));
                }
            } elseif (preg_match('/(.*?)\.php$/', $file, $match)) {
                $fileName = $match[1];
                if (in_array($fileName . '.php', $this->exceptions)) {
                    $classes[] = array_search($fileName . '.php', $this->exceptions);

                } else {
                    $classAtoms = explode('_', $match[1]);
                    array_walk($classAtoms, function (&$atom) {
                        $atom = ucfirst(strtolower($atom));
                    });
                    $classes[] = 'Recurly_' . join('', $classAtoms);
                }
            }
        }

        return $classes;
    }

    public function testRecurlyExists()
    {
        $actual = class_exists('\Simplerenew\Gateway\Recurly\AbstractRecurlyBase');
        $this->assertTrue($actual, 'Recurly Gateway was not found');
    }

    public function testAllClasses()
    {
        $this->assertTrue(true);
        foreach ($this->allClasses as $class) {
            $this->assertTrue(class_exists($class), "Class '{$class}' did not autoload");
        }
    }
}
