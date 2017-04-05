<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\FileLoader\MoFileLoader;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

/**
 * Class implemented by Symfony translation component
 *
 * @package Xinax\LaravelGettext\Translators
 */
class Symfony extends BaseTranslator
{

    /**
     * Symfony translator
     *
     * @var SymfonyTranslator
     */
    protected $symfonyTranslator;

    /**
     * @var array[]
     */
    protected $loadedResources = [];

    public function __construct(Config $config, AdapterInterface $adapter, FileSystem $fileSystem, Storage $storage)
    {
        parent::__construct($config, $adapter, $fileSystem, $storage);
        $this->setLocale($this->storage->getLocale());
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
        parent::setLocale($locale);
        $this->getTranslator()->setLocale($locale);
        $this->loadLocaleFile();

        if ($locale != $this->adapter->getLocale()) {
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
        parent::setDomain($domain);

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
     *
     * @return string
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
            [],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param $message
     * @param $amount
     *
     * @return string
     */
    public function translatePluralInline($message, $amount)
    {
        return $this->symfonyTranslator->transChoice(
            $message,
            $amount,
            [],
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
}