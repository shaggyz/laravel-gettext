<?php

namespace Xinax\LaravelGettext\Test;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \Mockery as m;
use \Xinax\LaravelGettext\LaravelGettext;
use \Xinax\LaravelGettext\Gettext;
use \Xinax\LaravelGettext\FileSystem;
use \Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;

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

    /**
     * Clear temportal files before each test
     */
    public function __construct()
    {
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

        $expected = [ 'views/misc' ];
        $result = $this->configManager->get()->getSourcesFromDomain('messages');
        $this->assertTrue($result === $expected);

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
        // Unit dir: we are here now :D
        $from = __DIR__;

        // Base path: tests/
        $to = $this->basePath;

        $result = $this->fileSystem->getRelativePath($to, $from);

        // Relative path from base path: ./unit/
        $this->assertSame("./unit/", $result);
    }

    /**
     * @expectedException Xinax\LaravelGettext\Exceptions\UndefinedDomainException
     */
    public function testTranslations()
    {
        // Session handler
        $session = m::mock('Xinax\LaravelGettext\Session\SessionHandler');
        $session->shouldReceive('get')->andReturn('es_AR');
        $session->shouldReceive('set');

        // Framework adapter
        $adapter = m::mock('Xinax\LaravelGettext\Adapters\LaravelAdapter');
        $adapter->shouldReceive('setLocale');
        $adapter->shouldReceive('getApplicationPath')->andReturn(dirname(__FILE__));

        $config = $this->configManager->get();

        // Static traslation files
        $config->setTranslationsPath("translations");
        $gettext = new Gettext($config, $session, $adapter, $this->fileSystem);
        $laravelGettext = new LaravelGettext($gettext);

        $laravelGettext->setLocale("es_AR");

        $this->assertSame("Cadena general con echo de php",
                                       _("general string with php echo"));

        $laravelGettext->setDomain("backend");

        $this->assertSame("backend", $laravelGettext->getDomain());
        $this->assertSame("Cadena en el backend con echo de php",
                                        _("Backend string with php echo"));

        $laravelGettext->setDomain("frontend");

        $this->assertSame("frontend", $laravelGettext->getDomain());
        $this->assertSame("Cadena de controlador",
                                _("Controller string"));
        $this->assertSame("Cadena de frontend con echo de php",
                                _("Frontend string with php echo"));

        $laravelGettext->setLocale("en_US");

        $this->assertSame("Frontend string with php echo",
                                _("Frontend string with php echo"));

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
