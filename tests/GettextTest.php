<?php

namespace Xinax\LaravelGettext\Test;
use \Mockery as m;
use \Xinax\LaravelGettext\Gettext;

class GettextTest extends \PHPUnit_Framework_TestCase  {

	protected $gettext;

	public function setUp(){

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

	public function testSetLocale(){
		// $this->gettext->setLocale('en_US');
	}

}
