<?php

namespace Xinax\LaravelGettext\Test;

use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;
use \Xinax\LaravelGettext\FileSystem;
use \Xinax\LaravelGettext\Config\ConfigManager;

class MultipleDomainTest extends BaseTestCase
{
    /**
     * FileSystem helper
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * Configuration manager
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        // $testConfig array
        include __DIR__ . '/../config/config.php';
        
        $this->configManager = ConfigManager::create($testConfig);
        $this->fileSystem = new FileSystem($this->configManager->get());

    }

    /**
     * Test domain configuration
     */
    public function testDomainConfiguration()
    {
        $expected = [
            'messages',
            'frontend',
            'backend',
        ];

        $this->assertTrue($this->configManager->get()->getAllDomains() === $expected);
    }    

    /**
     * View compiler tests
     */
    public function testCompileViews()
    {
        $viewPaths = [ __DIR__ . '/../views' ];
        $outputDirectory = __DIR__ . '/../storage';

        $result = $this->fileSystem->compileViews($viewPaths, $outputDirectory);
        $this->assertTrue($result);
    }

    /**
     * Mocker tear-down
     */
    public function tearDown()
    {
        m::close();
    }

}