<?php

namespace Xinax\LaravelGettext\Test;

use Mockery as m;
use Xinax\LaravelGettext\Adapters\LaravelAdapter;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Gettext;
use Xinax\LaravelGettext\LaravelGettext;
use Xinax\LaravelGettext\Session\SessionHandler;

/**
 * Class MultipleDomainTest
 * @package Xinax\LaravelGettext\Test
 */
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

        $actual = $this->configManager->get()->getAllDomains();
        $this->assertEquals($expected, $actual);
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
     * @expectedException \Xinax\LaravelGettext\Exceptions\UndefinedDomainException
     */
    public function testTranslations()
    {
        /** @var \Xinax\LaravelGettext\Session\SessionHandler|\Mockery\MockInterface $session */
        $session = m::mock(SessionHandler::class);
        $session->shouldReceive('get')
            ->andReturn('es_AR');

        $session->shouldReceive('set');

        /** @var \Xinax\LaravelGettext\Adapters\LaravelAdapter|\Mockery\MockInterface $adapter */
        $adapter = m::mock(LaravelAdapter::class);

        $adapter->shouldReceive('setLocale');
        $adapter->shouldReceive('getApplicationPath')
            ->andReturn(dirname(__FILE__));

        $config = $this->configManager->get();

        // Static traslation files
        $config->setTranslationsPath("translations");

        $gettext = new Gettext(
            $config,
            $session,
            $adapter,
            $this->fileSystem
        );

        $laravelGettext = new LaravelGettext($gettext);
        $laravelGettext->setLocale("es_AR");

        $this->assertSame(
            "Cadena general con echo de php",
            _("general string with php echo")
        );

        $laravelGettext->setDomain("backend");

        $this->assertSame(
            "backend",
            $laravelGettext->getDomain()
        );

        $this->assertSame(
            "Cadena en el backend con echo de php",
            _("Backend string with php echo")
        );

        $laravelGettext->setDomain("frontend");

        $this->assertSame(
            "frontend",
            $laravelGettext->getDomain()
        );

        $this->assertSame(
            "Cadena de controlador",
            _("Controller string")
        );
                                
        $this->assertSame(
            "Cadena de frontend con echo de php",
            _("Frontend string with php echo")
        );

        $laravelGettext->setLocale("en_US");

        $this->assertSame(
            "Frontend string with php echo",
            _("Frontend string with php echo")
        );

        // Expected exception
        $laravelGettext->setDomain("wrong-domain");
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
