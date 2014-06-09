<?php

namespace Xinax\LaravelGettext\Test;
use \Mockery as m;
use \Xinax\LaravelGettext\Gettext;

function app_path(){
	return 'hoa';
}

class GettextTest extends \PHPUnit_Framework_TestCase  {

	protected $gettext;

	public function setUp(){

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
		$model->shouldReceive('getSyncLaravel')->andReturn(false);

		$config = m::mock('Xinax\LaravelGettext\Config\ConfigManager');
		$config->shouldReceive('get')->andReturn($model);

		$session = m::mock('Xinax\LaravelGettext\Session\SessionHandler');
		$session->shouldReceive('get')->andReturn('en_US');
		$session->shouldReceive('set')->with('en_US');
		
		$this->gettext = new Gettext($config, $session);

	}

	public function testSetLocale(){
		$this->gettext->setLocale('en_US');
	}

}
