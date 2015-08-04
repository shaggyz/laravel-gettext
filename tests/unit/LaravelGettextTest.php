<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;

class LaravelGettextTest extends BaseTestCase
{
    /**
     * @var LaravelGettext
     */
    protected $laravelGettext;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $gettext = m::mock('Xinax\LaravelGettext\Gettext');

        $gettext->shouldReceive('getEncoding')->andReturn('UTF-8');
        $gettext->shouldReceive('setEncoding')->with('UTF-8');
        $gettext->shouldReceive('getLocale')->andReturn('en_US');
        $gettext->shouldReceive('setLocale')->with('en_US');
        $gettext->shouldReceive('filesystemStructure')->andReturn(true);

        $this->laravelGettext = new LaravelGettext($gettext);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test setting an instance.
     */
    public function testLaravelGettextInstance()
    {
        $this->assertInstanceOf('Xinax\LaravelGettext\LaravelGettext', $this->laravelGettext);
    }

    /**
     * Test getEncoding() method.
     */
    public function testGetEncoding()
    {
        $this->assertEquals('UTF-8', $this->laravelGettext->getEncoding());
    }

    /**
     * Test setEncoding() method.
     */
    public function testSetEncoding()
    {
        $this->laravelGettext->setEncoding('UTF-8');
    }

    /**
     * Test setLocale() method.
     */
    public function testSetLocale()
    {
        $this->laravelGettext->setLocale('en_US');
    }

    /**
     * Test getLocale() method.
     */
    public function testGetLocale()
    {
        $this->assertEquals('en_US', $this->laravelGettext->getLocale());
    }
}
