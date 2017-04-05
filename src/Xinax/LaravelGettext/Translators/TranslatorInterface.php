<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

interface TranslatorInterface
{

    /**
     * Initializes the module translator
     *
     * @param Config           $config
     * @param AdapterInterface $adapter
     * @param FileSystem       $fileSystem
     *
     * @param Storage          $storage
     */
    public function __construct(
        Config $config, AdapterInterface $adapter, FileSystem $fileSystem, Storage $storage);

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
     *
     * @return self
     */
    public function setEncoding($encoding);

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param   String $domain
     *
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
     *
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

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param $message
     * @param $amount
     *
     * @return string
     */
    public function translatePluralInline($message, $amount);
}