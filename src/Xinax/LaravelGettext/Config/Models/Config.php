<?php

namespace Xinax\LaravelGettext\Config\Models;

class Config
{

    /**
     * Session identifier
     *
     * @type String
     */
    protected $sessionIdentifier;

    /**
     * Charset encoding for files (UTF-8)
     *
     * @type
     */
    protected $encoding = 'UTF-8';

    /**
     * Full ISO Locale (en_EN)
     *
     * @type String
     */
    protected $locale;

    /**
     * Fallback locale
     *
     * @type String
     */
    protected $fallbackLocale;

    /**
     * Supported locales
     *
     * @type Array
     */
    protected $supportedLocales = array();

    /**
     * Gettext domain
     *
     * @type String
     */
    protected $domain;

    /**
     * Path to translation files
     *
     * @type String
     */
    protected $translationsPath;

    /**
     * Project identificator
     *
     * @type String
     */
    protected $project;

    /**
     * Translator contact data
     *
     * @type String
     */
    protected $translator;

    /**
     * Source paths
     *
     * @type Array
     */
    protected $sourcePaths = array();

    /**
     * Sync with laravel locale
     *
     * @type Boolean
     */
    protected $syncLaravel;

    /**
     * Gets the session identifier.
     *
     * @return mixed
     */
    public function getSessionIdentifier()
    {
        return $this->sessionIdentifier;
    }

    /**
     * Sets the session identifier.
     *
     * @param mixed $identifier the identifier
     * @return self
     */
    public function setSessionIdentifier($identifier)
    {
        $this->sessionIdentifier = $identifier;
        return $this;
    }

    /**
     * Gets the Charset encoding for files (UTF-8).
     *
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets the Charset encoding for files (UTF-8).
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Full ISO Locale (en_EN).
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the Full ISO Locale (en_EN).
     *
     * @param mixed $locale the locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Gets the Fallback locale.
     *
     * @return mixed
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * Sets the Fallback locale.
     *
     * @param mixed $fallbackLocale the fallback locale
     * @return self
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;
        return $this;
    }

    /**
     * Gets the Supported locales.
     *
     * @return mixed
     */
    public function getSupportedLocales()
    {
        return $this->supportedLocales;
    }

    /**
     * Sets the Supported locales.
     *
     * @param mixed $supportedLocales the supported locales
     * @return self
     */
    public function setSupportedLocales($supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
        return $this;
    }

    /**
     * Gets the Gettext domain.
     * \
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the Gettext domain.
     *
     * @param mixed $domain the domain
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Gets the Path to translation files.
     *
     * @return mixed
     */
    public function getTranslationsPath()
    {
        return $this->translationsPath;
    }

    /**
     * Sets the Path to translation files.
     *
     * @param mixed $translationsPath the translations path
     * @return self
     */
    public function setTranslationsPath($translationsPath)
    {
        $this->translationsPath = $translationsPath;
        return $this;
    }

    /**
     * Gets the Project identificator.
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Sets the Project identificator.
     *
     * @param mixed $project the project
     * @return self
     */
    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * Gets the Translator contact data.
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Sets the Translator contact data.
     *
     * @param mixed $translator the translator
     * @return self
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Gets the Source paths.
     *
     * @return mixed
     */
    public function getSourcePaths()
    {
        return $this->sourcePaths;
    }

    /**
     * Sets the Source paths.
     *
     * @param mixed $sourcePaths the source paths
     * @return self
     */
    public function setSourcePaths($sourcePaths)
    {
        $this->sourcePaths = $sourcePaths;
        return $this;
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
     * Sets the Sync with laravel locale.
     *
     * @param mixed $syncLaravel the sync laravel
     * @return self
     */
    public function setSyncLaravel($syncLaravel)
    {
        $this->syncLaravel = $syncLaravel;
        return $this;
    }

    /**
     * Returs an array with all domain names defined 
     * @return Array
     */
    public function getAllDomains()
    {
        $domains = [ $this->domain ];
        $userDomains = [];

        foreach ($this->sourcePaths as $domain => $paths) {
            if (is_array($paths)) {
                array_push($userDomains, $domain);
            }
        }

        return array_merge($domains, $userDomains);
    }

    /**
     * Returns all routes from a single domain
     * 
     * @param  string $domain Domain name
     * @return Array         
     */
    public function getSourcesFromDomain($domain) 
    {
        // Default domain
        if ($domain == $this->domain) {
            $rootPaths = [];
            foreach ($this->sourcePaths as $domain => $path) {
                if (!is_array($path)) {
                    array_push($rootPaths, $path);
                } 
            }
            return $rootPaths;
        } else if (array_key_exists($domain, $this->sourcePaths)) {
            return $this->sourcePaths[$domain];
        } else {
            return [];
        }
    }

}