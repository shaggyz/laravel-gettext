<?php

namespace Xinax\LaravelGettext\Test;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
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

        $result = $this->configManager->get()->getAllDomains();
        $this->assertTrue($result === $expected);

    }    

    public function testDomainPaths()
    {
        $expected = [
            'controllers',
            'views/frontend'
        ];

        $result = $this->configManager->get()->getSourcesFromDomain('frontend');
        $this->assertTrue($result === $expected);

        $expected = [ 'storage/views' ];
        $result = $this->configManager->get()->getSourcesFromDomain('messages');
        $this->assertTrue($result, $expected);

        $this->assertCount(0, $this->configManager->get()->getSourcesFromDomain('missing'));
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
     * Test the update 
     */
    public function notestFileSystem()
    {
        // Domain path test
        $domainPath = $this->fileSystem->getDomainPath();
        $this->fileSystem->checkBasePath();

        $this->assertTrue(is_dir($domainPath));
        $this->assertTrue(strpos($domainPath, 'i18n') !== false);

        // Locale path test
        $locale = 'es_AR';
        $localePath = $this->fileSystem->getDomainPath($locale);

        // Create locale test
        $localesGenerated = $this->fileSystem->generateLocales();

        $this->assertCount(2, $localesGenerated);
        $this->assertTrue($this->fileSystem->filesystemStructure());
        $this->assertTrue(is_dir($localePath));

        // Update locale test
        //$this->assertTrue($this->fileSystem->updateLocale($localePath, $locale, "backend"));
    }

    public function testGetRelativePath()
    {
        // Unit dir: 
        $from = __DIR__;

        // Base path: tests/
        $to = $this->configManager->get()->getBasePath();

        $result = $this->fileSystem->getRelativePath($to, $from);

        // Relative path from base path: ./unit/
        $this->assertSame("./unit/", $result);
    }

    public function testTranslations()
    {
        /**
         * @todo : test translations in different domains
         */
    }

    /**
     * Mocker tear-down
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Clear all files generated for testing purposes
     */
    protected function clearFiles()
    {
        $dir = __DIR__ . '/../lang';

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

    }

    public function __destruct()
    {
        $this->clearFiles();
    }

}