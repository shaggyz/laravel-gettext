<?php

namespace Xinax\LaravelGettext\Config\Models;

class Config
{
    /**
     * Session identifier
     *
     * @var string
     */
    protected $sessionIdentifier;

    /**
     * Charset encoding for files
     *
     * @var string
     */
    protected $encoding;

    /**
     * Full ISO Locale (en_EN)
     *
     * @var string
     */
    protected $locale;

    /**
     * Locale categories
     *
     * @type array
     */
    protected $categories;

    /**
     * Fallback locale
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * Supported locales
     *
     * @var array
     */
    protected $supportedLocales;

    /**
     * Gettext domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Path to translation files
     *
     * @var string
     */
    protected $translationsPath;

    /**
     * Project identifier
     *
     * @var string
     */
    protected $project;

    /**
     * Translator contact data
     *
     * @var string
     */
    protected $translator;

    /**
     * Source paths
     *
     * @var array
     */
    protected $sourcePaths;

    /**
     * Sync with laravel locale
     *
     * @type Boolean
     */
    protected $syncLaravel;

    /**
     * The adapter class used to sync with laravel locale
     *
     * @var string
     */
    protected $adapter;

    /**
     * Custom locale name
     * Used when needed locales are unavalilable
     *
     * @type Boolean
     */
    protected $customLocale;

    /**
     * Default relative path
     *
     * @type string
     */
    protected $relativePath;

    /**
     * Poedit keywords list
     *
     * @type array
     */
    protected $keywordsList;

    /**
     * Core translation handler
     */
    protected $handler;

    public function __construct()
    {
        $this->encoding = 'UTF-8';
        $this->supportedLocales = [];
        $this->sourcePaths = [];
        $this->customLocale = false;
        $this->relativePath = "../../../../../app";
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function setRelativePath($path)
    {
        $this->relativePath = $path;
    }

    /**
     * @return string
     */
    public function getSessionIdentifier()
    {
        return $this->sessionIdentifier;
    }

    /**
     * @param string $sessionIdentifier
     * @return $this
     */
    public function setSessionIdentifier($sessionIdentifier)
    {
        $this->sessionIdentifier = $sessionIdentifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Gets categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets categories
     *
     * @param array $categories
     * @return self
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string $fallbackLocale
     * @return $this
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;
        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedLocales()
    {
        return $this->supportedLocales;
    }

    /**
     * @param array $supportedLocales
     * @return $this
     */
    public function setSupportedLocales($supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationsPath()
    {
        return $this->translationsPath;
    }

    /**
     * @param string $translationsPath
     * @return $this
     */
    public function setTranslationsPath($translationsPath)
    {
        $this->translationsPath = $translationsPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param string $project
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param string $translator
     * @return $this
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return array
     */
    public function getSourcePaths()
    {
        return $this->sourcePaths;
    }

    /**
     * @param array $sourcePaths
     * @return $this
     */
    public function setSourcePaths($sourcePaths)
    {
        $this->sourcePaths = $sourcePaths;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSyncLaravel()
    {
        return $this->syncLaravel;
    }

    /**
     * Gets the Sync with laravel locale.
     *
     * @return mixed
     */
    public function getSyncLaravel()
    {
        return $this->syncLaravel;
    }

    /**
     * @param boolean $syncLaravel
     * @return $this
     */
    public function setSyncLaravel($syncLaravel)
    {
        $this->syncLaravel = $syncLaravel;
        return $this;
    }

    /**
     * Gets the adapter class.
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Return an array with all domain names
     *
     * @return array
     */
    public function getAllDomains()
    {
        $domains = [$this->domain]; // add the default domain

        foreach ($this->sourcePaths as $domain => $paths) {
            if (is_array($paths)) {
                array_push($domains, $domain);
            }
        }

        return array_unique($domains);
    }

    /**
     * Return all routes for a single domain
     *
     * @param $domain
     * @return array
     */
    public function getSourcesFromDomain($domain)
    {
        // grab any paths wrapped in $domain
        $explicitPaths = array_key_exists($domain, $this->sourcePaths) ? $this->sourcePaths[$domain] : [];

        // if we're not including the default domain, return what we have so far
        if ($this->domain != $domain) {
            return $explicitPaths;
        }

        // otherwise, grab all the default domain paths
        // and merge them with paths wrapped in $domain
        return array_reduce(
            $this->sourcePaths,
            function ($carry, $path) {
                if (!is_array($path)) {
                    $carry[] = $path;
                }

                return $carry;
            },
            $explicitPaths
        );
    }

    /**
     * Gets C locale setting.
     *
     * @return boolean
     */
    public function getCustomLocale()
    {
        return $this->customLocale;
    }

    /**
     * Sets if will use C locale structure.
     *
     * @param mixed $sourcePaths the source paths
     * @return self
     */
    public function setCustomLocale($customLocale)
    {
        $this->customLocale = $customLocale;
        return $this;
    }

    /**
     * Gets the Poedit keywords list.
     *
     * @return mixed
     */
    public function getKeywordsList()
    {
        return !empty($this->keywordsList) ? $this->keywordsList : ['_'];
    }

    /**
     * Sets the Poedit keywords list.
     *
     * @param mixed $keywordsList the keywords list
     *
     * @return self
     */
    public function setKeywordsList($keywordsList)
    {
        $this->keywordsList = $keywordsList;

        return $this;
    }

    /**
     * Sets the handler type. Also check for valid handler name
     *
     * @param $handler
     * @return $this
     *
     * @throws \Exception
     */
    public function setHandler($handler)
    {
        if (!in_array($handler, [
            'symfony',
            'gettext'
        ])) {
            throw new \Exception("Handler '$handler' is not supported'");
        };

        $this->handler = $handler;
        return $this;
    }

    /**
     * Returns the handler name
     *
     * @return mixed
     */
    public function getHandler()
    {
        return !empty($this->handler) ? $this->handler : 'symfony';
    }
}
