<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\FileSystem;

interface TranslatorInterface
{
    /**
     * TranslatorInterface constructor.
     *
     * @param Config $config
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     */
    public function __construct(
        Config $config,
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
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales();

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
     * @throws  \Xinax\LaravelGettext\Exceptions\UndefinedDomainException If domain is not defined
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

    /**
     * Translates a plural string
     *
     * @param $singular
     * @param $plural
     * @param $count
     *
     * @return mixed
     */
    public function translatePlural($singular, $plural, $count);
}