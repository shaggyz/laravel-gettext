<?php

namespace Xinax\LaravelGettext\Config;

use Xinax\LaravelGettext\Testing\BaseTestCase;

/**
 * Test class for testing the logic provided by the ConfigManager-class
 */
class ConfigManagerTest extends BaseTestCase
{
    /**
     * Base app path
     *
     * @var string
     */
    protected $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';
    /**
     * Base config array used in the test suite
     * @var array
     */
    private $baseConfig;

    protected function setUp()
    {
        parent::setUp();

        $this->baseConfig = include __DIR__ . '/../config/config.php';
    }

    /**
     * Test that when multiple categories are defined in the config-file, they
     * are properly set in the Config-model.
     *
     */
    public function testSetMultipleCategories()
    {
        // Define the expected result
        $expected = [
            'LC_ALL',
            'LANG',
        ];

        // Setup the config for this test by overwriting default config with
        // specific values for this test
        $testConfig = array_merge($this->baseConfig, [
            'categories' => ['LC_ALL', 'LANG']
        ]);

        // Create a new instance of the config manager with the config
        $configModel = ConfigManager::create($testConfig)->get();

        // Get the actual result
        $actual = $configModel->getCategories();

        // Make the assertion
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that when one category is defined in the config-file, it is set in
     * the Config-model
     */
    public function testNonDefaultCategory()
    {
        // Define the expected result
        $expected = [
            'LANG',
        ];

        // Setup the config for this test by overwriting default config with
        // specific values for this test
        $testConfig = array_merge($this->baseConfig, [
            'categories' => ['LANG']
        ]);

        // Create a new instance of the config manager with the config
        $configModel = ConfigManager::create($testConfig)->get();

        // Get the actual result
        $actual = $configModel->getCategories();

        // Make the assertion
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that when no categories are defined in the config-file, the
     * Config-model is set with no categories.
     */
    public function testNoCategory()
    {
        // Define the expected result
        $expected = [];

        // Setup the config for this test by overwriting default config with
        // specific values for this test
        $testConfig = array_merge($this->baseConfig, [
            'categories' => []
        ]);

        // Create a new instance of the config manager with the config
        $configModel = ConfigManager::create($testConfig)->get();

        // Get the actual result
        $actual = $configModel->getCategories();

        // Make the assertion
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that when no categories-key is present in the config , the
     * Config-model is set to the default category "LC_ALL".
     */
    public function testDefaultCategory()
    {
        // Define the expected result
        $expected = [
            'LC_ALL'
        ];

        // Setup the config for this test by removing the categories key
        $testConfig = $this->baseConfig;
        unset($testConfig['categories']);

        // Create a new instance of the config manager with the config
        $configModel = ConfigManager::create($testConfig)->get();

        // Get the actual result
        $actual = $configModel->getCategories();

        // Make the assertion
        $this->assertEquals($expected, $actual);
    }
}
