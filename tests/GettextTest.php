<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\Gettext;

/**
 * Class GettextTest
 * @package Xinax\LaravelGettext\Test
 */
class GettextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Gettext
     */
    protected $gettext;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        // Config
        $model = m::mock('Xinax\LaravelGettext\Config\Models\Config');
        $model->shouldReceive('getEncoding')->andReturn('UTF-8');
        $model->shouldReceive('getLocale')->andReturn('en_US');
        $model->shouldReceive('getSupportedLocales')->andReturn(array(
            'es_AR',
            'en_US',
            'it_IT',
        ));
        $model->shouldReceive('getFallbackLocale')->andReturn('en_US');
        $model->shouldReceive('getDomain')->andReturn('messages');
        $model->shouldReceive('getSyncLaravel')->andReturn(true);
        $model->shouldReceive('getTranslationsPath')->andReturn('lang');

        // ConfigManager
        $config = m::mock('Xinax\LaravelGettext\Config\ConfigManager');
        $config->shouldReceive('get')->andReturn($model);

        // Session handler
        $session = m::mock('Xinax\LaravelGettext\Session\SessionHandler');
        $session->shouldReceive('get')->andReturn('en_US');
        $session->shouldReceive('set')->with('en_US');

        // Framework adapter
        $adapter = m::mock('Xinax\LaravelGettext\Adapters\LaravelAdapter');
        $adapter->shouldReceive('setLocale')->with('en_US');
        $adapter->shouldReceive('getApplicationPath')->andReturn(dirname(__FILE__));

        $this->gettext = new Gettext($config, $session, $adapter);

    }

    /**
     * test setLocaleMethod.
     */
    public function testSetLocale()
    {
        $response = $this->gettext->setLocale('en_US');
        $this->assertNotEmpty($response);
        $this->assertTrue($response != 'en_US');
        $this->assertTrue($response != '.');
        $this->assertTrue($response != '.UTF-8');
        $this->assertEquals('en_US.UTF-8', $response);
    }

    /**
     * Test getLocale() method.
     */
    public function testGetLocale()
    {
        $response = $this->gettext->getLocale();
        $this->assertNotEmpty($response);
        $this->assertTrue($response != 'en_US');
        $this->assertTrue($response != '.');
        $this->assertTrue($response != '.UTF-8');
        $this->assertEquals('en_US.UTF-8', $response);
    }

    /**
     * Test if isLocaleSupported() method returns true.
     */
    public function testIsLocaleSupported()
    {
        $this->assertTrue($this->gettext->isLocaleSupported('en_US'));
    }

    /**
     * Test filesystemStructure() method.
     */
    public function testFilesystemStructure()
    {
        $this->assertTrue($this->gettext->filesystemStructure());
    }

    /**
     * Test toString() method.
     */
    public function testToString()
    {
        $response = $this->gettext->__toString();
        $this->assertNotEmpty($response);
        $this->assertTrue($response != 'en_US');
        $this->assertTrue($response != '.');
        $this->assertTrue($response != '.UTF-8');
        $this->assertEquals('en_US.UTF-8', $response);
    }

    /**
     * Test get encoding.
     */
    public function testGetEncoding()
    {
        $response = $this->gettext->getEncoding();
        $this->assertNotEmpty($response);
        $this->assertEquals('UTF-8', $response);
    }

    /**
     * Test set encoding.
     */
    public function testSetEncoding()
    {
        $response = $this->gettext->setEncoding('UTF-8');
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('Xinax\LaravelGettext\Gettext', $response);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        m::close();
    }

}
