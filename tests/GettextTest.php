<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\Gettext;

class GettextTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Gettext wrapper
	 * @var Xinax\LaravelGettext\Gettext
	 */
	protected $gettext;

	public function setUp()
	{
		parent::setUp();

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
     * Test setting locale.
     */
	public function testSetLocale()
	{
		$response = $this->gettext->setLocale('en_US');

        $this->assertEquals('en_US', $response);
	}

    /**
     * Test getting locale.
     * It should receive locale from mocked config.
     */
	public function testGetLocale()
	{
		$response = $this->gettext->getLocale();

		$this->assertEquals('en_US', $response);
	}

	public function testIsLocaleSupported()
	{
		$this->assertTrue($this->gettext->isLocaleSupported('en_US'));
	}

	public function testFilesystemStructure()
	{
		$this->assertTrue($this->gettext->filesystemStructure());
	}

    /**
     * Test dumping locale to string
     */
	public function testToString()
	{
		$response = $this->gettext->__toString();

		$this->assertEquals('en_US', $response);
	}

	public function testGetEncoding()
	{
		$response = $this->gettext->getEncoding();
		$this->assertNotEmpty($response);
		$this->assertEquals('UTF-8', $response);
	}

	public function testSetEncoding()
	{
		$response = $this->gettext->setEncoding('UTF-8');
		$this->assertNotEmpty($response);
		$this->assertInstanceOf('Xinax\LaravelGettext\Gettext', $response);
	}

	public function tearDown()
	{
		m::close();
	}

}
