<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Session;

class BaseTranslator
{
    /**
     * Config container
     *
     * @type \Xinax\LaravelGettext\Config\Models\Config
     */
    protected $configuration;

    /**
     * Framework adapter
     *
     * @type \Xinax\LaravelGettext\Adapters\LaravelAdapter
     */
    protected $adapter;

    /**
     * File system helper
     *
     * @var \Xinax\LaravelGettext\FileSystem
     */
    protected $fileSystem;

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale()
    {
        return $this->sessionGet('locale', $this->configuration->getLocale());
    }

    /**
     * Sets and stores on session the current locale code
     *
     * @return BaseTranslator
     */
    public function setLocale($locale)
    {
        $this->sessionSet('locale', $locale);
        return $this;
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
        return $this->sessionGet('encoding', $this->configuration->getEncoding());
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->sessionSet('encoding', $encoding);
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

        $this->sessionSet('domain', $domain);
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain()
    {
        return $this->sessionGet('domain', $this->configuration->getDomain());
    }

    /**
     * Return a value from session with an optional default
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    protected function sessionGet($key, $default=null)
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;
        return Session::get($token, $default);
    }

    /**
     * Sets a value in session session
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function sessionSet($key, $value)
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;
        Session::put($token, $value);
        return $this;
    }

    /**
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales()
    {
        return $this->configuration->getSupportedLocales();
    }

}