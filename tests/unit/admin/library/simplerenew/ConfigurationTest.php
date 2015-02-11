<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Configuration;

defined('_JEXEC') or die();

/**
 * Class ConfigurationTest
 *
 * @TODO: Create additional tests
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $sampleData = array(
        'level1'  => 'level1',
        'layer1'  => array(
            'level2' => 'level2',
        ),
        'layer1a' => array(
            'layer2' => array(
                'level3' => 'level3'
            )
        )
    );

    /**
     * @var Configuration
     */
    protected $sample = null;

    public function setUp()
    {
        $this->sample = new Configuration($this->sampleData);
    }

    public function testGetNotExist()
    {
        $expect = 'Not Set';
        $actual = $this->sample->get('foobar', $expect);
        $this->assertEquals($expect, $actual);
    }

    public function testGetSingle()
    {
        $actual = $this->sample->get('level1');
        $expect = $this->sampleData['level1'];
        $this->assertEquals($actual, $expect);
    }

    public function testGetLevel2()
    {
        $actual = $this->sample->get('layer1.level2');
        $expect = $this->sampleData['layer1']['level2'];
        $this->assertEquals($expect, $actual);
    }

    public function testGetLevel3()
    {
        $actual = $this->sample->get('layer1a.layer2.level3');
        $expect = $this->sampleData['layer1a']['layer2']['level3'];
        $this->assertEquals($expect, $actual);
    }

    public function testGetConfig()
    {
        $actual = $this->sample->getConfig('layer1');
        $expect = new Configuration($this->sampleData['layer1']);
        $this->assertEquals($expect, $actual);
    }
}
