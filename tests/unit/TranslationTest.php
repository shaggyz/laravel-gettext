<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 17-08-01
 * Time: 10:22
 */

namespace unit;

use Xinax\LaravelGettext\Adapters\LaravelAdapter;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\MemoryStorage;
use Xinax\LaravelGettext\Testing\BaseTestCase;
use Xinax\LaravelGettext\Translators\Symfony;

class TranslationTest extends BaseTestCase
{

    /**
     * Base app path
     *
     * @var string
     */
    protected $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';
    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var Symfony
     */
    protected $translator;

    public function setUp()
    {
        parent::setUp();
        $testConfig = include __DIR__ . '/../config/config_fr.php';

        $config           = ConfigManager::create($testConfig);
        $adapter          = new LaravelAdapter();
        $this->fileSystem = new FileSystem($config->get(), __DIR__ . '/../', __DIR__ . '/../storage');

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $this->fileSystem,
            new MemoryStorage($config->get())
        );

        $this->translator = $translator;
    }

    /**
     * View compiler tests
     */
    public function testCompileViews()
    {
        $viewPaths = ['views'];

        $result = $this->fileSystem->compileViews($viewPaths, "messages");
        $this->assertTrue($result);

    }

    public function testFrenchTranslation()
    {
        $string = $this->translator->setLocale('fr_FR')->translate('Controller string');
        $this->assertEquals('Chaine de caractère du controlleur', $string);
    }

    public function testFrenchTranslationReplacement()
    {
        $string = $this->translator->setLocale('fr_FR')->translate('Hello %s, how are you ?');
        $this->assertEquals('Salut %s, comment va ?', $string);
    }

    public function testFrenchTranslationPluralNone()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       0);
        $this->assertEquals('Il n\'y a pas de pommes', $string);
    }

    public function testFrenchTranslationPluralOne()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       1);
        $this->assertEquals('Il y a une pomme', $string);
    }

    public function testFrenchTranslationPluralMultiple()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       5);
        $this->assertEquals('Il y a 5 pommes', $string);
    }
}
