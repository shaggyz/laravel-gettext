<?php

namespace Xinax\LaravelGettext\Test;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;
use \Xinax\LaravelGettext\Translators\Gettext;
use \Xinax\LaravelGettext\FileSystem;
use \Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Illuminate\Foundation\Testing\TestCase;
use Xinax\LaravelGettext\Translators\Symfony;

/**
 * Class MultipleDomainTest
 * @package Xinax\LaravelGettext\Test
 */
class MultipleDomainTest extends TestCase
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
     * Testing base path
     * @var String
     */
    protected $basePath;

    /**
     * Testing storage path
     * @var String
     */
    protected $storagePath;

    public function __construct()
    {
        parent::__construct();

        $this->clearFiles();
    }

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';

        $app->register('Xinax\LaravelGettext\LaravelGettextServiceProvider');

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        // $testConfig array
        $testConfig = include __DIR__ . '/../config/config.php';
        $this->configManager = ConfigManager::create($testConfig);

        $this->basePath = realpath(__DIR__ . '/..');
        $this->storagePath = realpath(__DIR__ . '/../storage');

        $this->fileSystem = new FileSystem($this->configManager->get(),
            $this->basePath,
            $this->storagePath
        );

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

    public function testFrontendDomainPaths()
    {
        $expectedPaths = [
            'controllers',
            'views/frontend'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('frontend');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testBackendDomainPaths()
    {
        $expectedPaths = [
            'views/backend'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('backend');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testDefaultDomainPaths()
    {
        $expectedPaths = [
            'views/misc'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('messages');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testNoMissingDomainPaths()
    {
        // config/config.php doesn't contain a domain named `missing`, and should return no records
        $this->assertCount(0, $this->configManager->get()->getSourcesFromDomain('missing'));
    }

    /**
     * View compiler tests
     */
    public function testCompileViews()
    {
        $viewPaths = [ 'views' ];

        $result = $this->fileSystem->compileViews($viewPaths, "frontend");
        $this->assertTrue($result);

    }


    /**
     * Test the update
     */
    public function testFileSystem()
    {
        // Domain path test
        $domainPath = $this->fileSystem->getDomainPath();

        // Locale path test
        $locale = 'es_AR';
        $localePath = $this->fileSystem->getDomainPath($locale);

        // Create locale test
        $localesGenerated = $this->fileSystem->generateLocales();
        $this->assertTrue($this->fileSystem->checkDirectoryStructure(true));

        $this->assertCount(3, $localesGenerated);
        $this->assertTrue(is_dir($domainPath));
        $this->assertTrue(strpos($domainPath, 'i18n') !== false);

        foreach ($localesGenerated as $localeGenerated) {
            $this->assertTrue(file_exists($localeGenerated));
        }

        $this->assertTrue(is_dir($localePath));

        // Update locale test
        $this->assertTrue($this->fileSystem->updateLocale($localePath, $locale, "backend"));
    }

    public function testGetRelativePath()
    {
        // dir/
        $from = __DIR__;
        $to = dirname(dirname(__DIR__));

        $result = $this->fileSystem->getRelativePath($to, $from);

        // Relative path from base path: unit/
        $this->assertSame("unit/", $result);
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
        $dir = __DIR__ . '/../lang/i18n';
        FileSystem::clearDirectory($dir);
    }
}
