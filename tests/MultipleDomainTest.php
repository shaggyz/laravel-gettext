<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;

class MultipleDomainTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LaravelGettext
     */
    protected $laravelGettext;

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        return require __DIR__.'/../vendor/autoload.php';
        $unitTesting = true;
 
        $testEnvironment = 'testing';
 
    }   

    

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

    public function tearDown()
    {
        m::close();
    }

}