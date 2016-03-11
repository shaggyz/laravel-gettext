<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Session\SessionHandler;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\FileSystem;

interface TranslatorInterface
{
    /**
     * TranslatorInterface constructor.
     *
     * @param Config $config
     * @param SessionHandler $sessionHandler
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     */
    public function __construct(
        Config $config,
        SessionHandler $sessionHandler,
        AdapterInterface $adapter,
        FileSystem $fileSystem
    );

    /**
     * Sets the current locale code
     */
    public function setLocale($locale);

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale();

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @return boolean
     */
    public function isLocaleSupported($locale);

    /**
     * Return the current locale
     *
     * @return mixed
     */
    public function __toString();

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding();

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding);

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param   String $domain
     * @throws  Exceptions\UndefinedDomainException If domain is not defined
     * @return  self
     */
    public function setDomain($domain);

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain();

    /**
     * Translates a single message
     *
     * @param $message
     * @return string
     */
    public function translate($message);
}