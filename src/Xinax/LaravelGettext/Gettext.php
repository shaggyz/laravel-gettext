<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Session\SessionHandler;
use Xinax\LaravelGettext\Adapters\AdapterInterface;

use \Session;

class Gettext
{
    /**
     * Config container
     * @type Xinax\LaravelGettext\Config\Models\Config
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
     * @type Xinax\Adapters\LaravelAdapter
     */
    protected $adapter;

    /**
     * File system helper
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * Sets the configuration and session manager
     */
    public function __construct(
        ConfigManager $configMan, 
        SessionHandler $sessionHandler, 
        AdapterInterface $adapter
    ){
        // Sets the package configuration and session handler
        $this->configuration = $configMan->get();
        $this->session = $sessionHandler;
        $this->adapter = $adapter;
        $this->fileSystem = new FileSystem($this->configuration);

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
            $domain = $this->configuration->getDomain();
            $gettextLocale = $locale . "." . $this->encoding;

            putenv("LC_ALL=$gettextLocale");
            setlocale(LC_ALL, $gettextLocale);
            bindtextdomain($domain, $this->fileSystem->getDomainPath());
            textdomain($domain);

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
}
