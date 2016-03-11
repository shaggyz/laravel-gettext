<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;

class BaseTranslator
{
    /**
     * Config container
     * @type \Xinax\LaravelGettext\Config\Models\Config
     */
    protected $configuration;

    /**
     * Current encoding
     * @type String
     */
    protected $encoding;

    /**
     * Current locale
     * @type String
     */
    protected $locale;

    /**
     * Framework adapter
     * @type \Xinax\LaravelGettext\Adapters\LaravelAdapter
     */
    protected $adapter;

    /**
     * File system helper
     * @var \Xinax\LaravelGettext\FileSystem
     */
    protected $fileSystem;

    /**
     * Domain name
     * @var String
     */
    protected $domain;

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets and stores on session the current locale code
     */
    public function setLocale($locale)
    {
        $this->session->set($locale);
        $this->locale = $locale;
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @return boolean
     */
    public function isLocaleSupported($locale)
    {
        if ($locale) {
            return in_array($locale, $this->configuration->getSupportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->getLocale();
    }

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets the Current encoding.
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
     * Sets the current domain and updates gettext domain application
     *
     * @param   String                      $domain
     * @throws  UndefinedDomainException    If domain is not defined
     * @return  self
     */
    public function setDomain($domain)
    {
        if (!in_array($domain, $this->configuration->getAllDomains())) {
            throw new UndefinedDomainException("Domain '$domain' is not registered.");
        }

        $this->domain = $domain;
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain()
    {
        return $this->domain;
    }

}