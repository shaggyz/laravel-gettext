<?php

use \Mockery as m;

class LaravelGettextTest extends \PHPUnit_Framework_TestCase {

	protected $laravelGettext;

	public function setUp(){

		$gettext = m::mock('Xinax\LaravelGettext\Gettext');
		
		$gettext->shouldReceive('getEncoding')->andReturn('UTF-8');
		$gettext->shouldReceive('setEncoding')->with('UTF-8');
		$gettext->shouldReceive('getLocale')->andReturn('en_US');
		$gettext->shouldReceive('setLocale')->with('en_US');

		$this->laravelGettext = new Xinax\LaravelGettext\LaravelGettext($gettext);

	}

	public function tearDown(){
		m::close();
	}

	public function testLaravelGettextInstance(){
		$this->assertInstanceOf('Xinax\LaravelGettext\LaravelGettext', $this->laravelGettext);
	}

	public function testCheckDependencies(){
		$this->assertTrue($this->laravelGettext->checkDependencies());
	}

	public function testGetEncoding(){
		$this->assertEquals('UTF-8', $this->laravelGettext->getEncoding());
	}

	public function testSetEncoding(){
		$this->laravelGettext->setEncoding('UTF-8');
	}

	public function testSetLocale(){
		$this->laravelGettext->setLocale('en_US');
	}

	public function testGetLocale(){
		$this->assertEquals('en_US', $this->laravelGettext->getLocale());
	}


}
