<?php

namespace Xinax\LaravelGettext\Config;

use \Xinax\LaravelGettext\Config\Models\Config as ConfigModel;
use \Xinax\LaravelGettext\Exceptions\RequiredConfigurationFileException;
use \Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use \Illuminate\Support\Facades\Config;

class ConfigManager
{
    /**
     * Config model
     *
     * @var ConfigModel
     */
    protected $config;

    /**
     * Package configuration route (published)
     */
    const DEFAULT_PACKAGE_CONFIG = 'laravel-gettext';

    /**
     * @param array $config
     * @throws RequiredConfigurationKeyException
     */
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $this->generateFromArray($config);
        } else {
            // In Laravel 5.3 we need empty config model
            $this->config = new ConfigModel;
        }
    }

    /**
     * Get new instance of the ConfigManager
     *
     * @param null $config
     * @return static
     * @throws RequiredConfigurationFileException
     */
    public static function create($config = null)
    {
        if (is_null($config)) {
            // Default package configuration file (published)
            $config = Config::get(static::DEFAULT_PACKAGE_CONFIG);
        }

        return new static($config);
    }

    /**
     * Get the config model
     *
     * @return ConfigModel
     */
    public function get()
    {
        return $this->config;
    }

    /**
     * Creates a configuration container and checks the required fields
     *
     * @param array $config
     * @return ConfigModel
     * @throws RequiredConfigurationKeyException
     */
    protected function generateFromArray(array $config)
    {
        $requiredKeys = [
            'locale',
            'fallback-locale',
            'encoding'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RequiredConfigurationKeyException(
                    sprintf('Unconfigured required value: %s', $key)
                );
            }
        }

        $container = new ConfigModel();

        $id = isset($config['session-identifier']) ? $config['session-identifier'] : 'laravel-gettext-locale';

        $adapter = isset($config['adapter']) ? $config['adapter'] : \Xinax\LaravelGettext\Adapters\LaravelAdapter::class;

        $container->setLocale($config['locale'])
            ->setSessionIdentifier($id)
            ->setEncoding($config['encoding'])
            ->setCategories(array_get('categories', $config, ['LC_ALL']))
            ->setFallbackLocale($config['fallback-locale'])
            ->setSupportedLocales($config['supported-locales'])
            ->setDomain($config['domain'])
            ->setTranslationsPath($config['translations-path'])
            ->setProject($config['project'])
            ->setTranslator($config['translator'])
            ->setSourcePaths($config['source-paths'])
            ->setSyncLaravel($config['sync-laravel'])
            ->setAdapter($adapter);

        if (array_key_exists('relative-path', $config)) {
            $container->setRelativePath($config['relative-path']);
        }

        if (array_key_exists("custom-locale", $config)) {
            $container->setCustomLocale($config['custom-locale']);
        }

        if (array_key_exists("keywords-list", $config)) {
            $container->setKeywordsList($config['keywords-list']);
        }

        if (array_key_exists("handler", $config)) {
            $container->setHandler($config['handler']);
        }

        return $container;
    }
}
