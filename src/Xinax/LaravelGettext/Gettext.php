<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Session\SessionHandler;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;

use Illuminate\Support\Facades\Session;

class Gettext
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
     * @type \Xinax\Adapters\LaravelAdapter
     */
    protected $adapter;

    /**
     * File system helper
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var String
     */
    protected $domain;

    /**
     * Sets the configuration and session manager
     */
    public function __construct(
        Config $config,
        SessionHandler $sessionHandler, 
        AdapterInterface $adapter,
        FileSystem $fileSystem
    ){
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->session = $sessionHandler;
        $this->adapter = $adapter;
        $this->fileSystem = $fileSystem;

        // General domain
        $this->domain = $this->configuration->getDomain();

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();

        // Sets defaults for boot
        $locale = $this->session->get($this->configuration->getLocale());

        $this->setLocale($locale);
    }

    /**
     * Sets the current locale code
     */
    public function setLocale($locale)
    {
        if (!$this->isLocaleSupported($locale)) {
            throw new Exceptions\LocaleNotSupportedException(
                "Locale $locale is not supported");
        }

        try {
            $gettextLocale = $locale . "." . $this->encoding;

            // All locale functions are updated: LC_COLLATE, LC_CTYPE,
            // LC_MONETARY, LC_NUMERIC, LC_TIME and LC_MESSAGES
            putenv("LC_ALL=$gettextLocale");
            putenv("LANGUAGE=$gettextLocale");
            setlocale(LC_ALL, $gettextLocale);

            // Domain
            $this->setDomain($this->domain);

            $this->locale = $locale;
            $this->session->set($locale);

            // Laravel built-in locale
            if ($this->configuration->getSyncLaravel()) {
                $this->adapter->setLocale($locale);
            }

            return $this->getLocale();

        } catch (\Exception $e) {

            $this->locale = $this->configuration->getFallbackLocale();
            $exceptionPosition = $e->getFile() . ":" . $e->getLine();
            throw new \Exception($exceptionPosition . $e->getMessage());

        }
    }

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

        bindtextdomain($domain, $this->fileSystem->getDomainPath());
        bind_textdomain_codeset($domain, $this->encoding);

        $this->domain = textdomain($domain);

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
