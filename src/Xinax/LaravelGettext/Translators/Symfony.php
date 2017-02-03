<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\FileSystem;

/**
 * Class implemented by Symfony translation component
 *
 * @package Xinax\LaravelGettext\Translators
 */
class Symfony implements TranslatorInterface
{

    /**
     * Current encoding
     *
     * @type String
     */
    protected $encoding;

    /**
     * @var String
     */
    protected $domain;

    /**
     * Symfony translator
     *
     * @var SymfonyTranslator
     */
    protected $symfonyTranslator;


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
     * @var array[]
     */
    protected $loadedResources = [];

    /**
     * TranslatorInterface constructor.
     *
     * @param Config           $config
     * @param AdapterInterface $adapter
     * @param FileSystem       $fileSystem
     */
    public function __construct(
        Config $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter       = $adapter;
        $this->fileSystem    = $fileSystem;

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();
        $this->loadLocaleFile();

    }

    /**
     * Translates a message using the Symfony translation component
     *
     * @param $message
     *
     * @return string
     */
    public function translate($message)
    {
        return $this->symfonyTranslator->trans($message, [], $this->getDomain(), $this->getLocale());
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator()
    {
        if (isset($this->symfonyTranslator)) {
            return $this->symfonyTranslator;
        }

        return $this->symfonyTranslator = $this->createTranslator();
    }

    /**
     * Set locale overload.
     * Needed to re-build the catalogue when locale changes.
     *
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->getTranslator()->setLocale($locale);
        $this->loadLocaleFile();

        if($locale != $this->adapter->getLocale()) {
            $this->adapter->setLocale($locale);
        }

        return $this;
    }

    /**
     * Set domain overload.
     * Needed to re-build the catalogue when domain changes.
     *
     *
     * @param String $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        $this->loadLocaleFile();

        return $this;
    }

    /**
     * Creates a new translator instance
     *
     * @return SymfonyTranslator
     */
    protected function createTranslator()
    {
        $translator = new SymfonyTranslator($this->configuration->getLocale());
        $translator->setFallbackLocales([$this->configuration->getFallbackLocale()]);
        $translator->addLoader('mo', new MoFileLoader());
        $translator->addLoader('po', new PoFileLoader());

        return $translator;
    }

    /**
     * Translates a plural string
     *
     * @param $singular
     * @param $plural
     * @param $amount
     */
    public function translatePlural($singular, $plural, $amount)
    {
        return $this->symfonyTranslator->transChoice(
        // Symfony translator looks for 'singular|plural' message id in catalog,
        // and obviously doesn't exists, so always the fallback string will be returned.
        // $singular . '|' . $plural, //<-- this just doesn't works, idk wtf is wrong.
            $amount > 1
                ? $plural
                : $singular,
            $amount,
            ['%count%' => $amount],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * @internal param $translator
     */
    protected function loadLocaleFile()
    {
        if (isset($this->loadedResources[$this->getDomain()])
            && isset($this->loadedResources[$this->getDomain()][$this->getLocale()])
        ) {
            return;
        }
        $translator = $this->getTranslator();

        $fileMo = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain(), 'mo');
        if (file_exists($fileMo)) {
            $translator->addResource('mo', $fileMo, $this->getLocale(), $this->getDomain());
        } else {
            $file = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain());
            $translator->addResource('po', $file, $this->getLocale(), $this->getDomain());
        }

        $this->loadedResources[$this->getDomain()][$this->getLocale()] = true;
    }

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale()
    {
        return $this->getTranslator()->getLocale();
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param $locale
     *
     * @return bool
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
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales()
    {
        return $this->configuration->getSupportedLocales();
    }

    /**
     * Getter for encoding
     *
     * @return String
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     *
     * @param mixed $encoding
     *
     * @return TranslatorInterface
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

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