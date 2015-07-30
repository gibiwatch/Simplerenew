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
                if ($fileName == 'errors') {
                    // Checks that all classes in the errors file autoload
                    $fileText = file_get_contents($path . '/' . $file);
                    preg_match_all('/^class\s+(Recurly_[^\s]*)\s/m', $fileText, $matches);

                    $classes = array_merge($classes, $matches[1]);

                } elseif (in_array($fileName . '.php', $this->exceptions)) {
                    // Files/Classes that don't follow the normal conventions
                    $classes[] = array_search($fileName . '.php', $this->exceptions);

                } else {
                    // underscore in filename creates camelBase
                    $classAtoms = explode('_', $match[1]);
                    array_walk($classAtoms, function (&$atom) {
                        $atom = ucfirst(strtolower($atom));
                    });
                    // All classes use the same prefix
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
