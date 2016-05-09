<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;
use \Xinax\LaravelGettext\Config\ConfigManager;
use \Xinax\LaravelGettext\Adapters\LaravelAdapter;
use \Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Translators\Symfony;

class LaravelGettextTest extends BaseTestCase
{
    /**
     * Gettext wrapper
     * @var \Xinax\LaravelGettext\Gettext
     */
    protected $gettext;

    public function setUp()
    {
        parent::setUp();

        $config = ConfigManager::create();
        $adapter = new LaravelAdapter;

        $fileSystem = new FileSystem($config->get(), app_path(), storage_path());

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $fileSystem
        );

        $this->gettext = $translator;
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
