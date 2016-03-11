<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Cache;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Session\SessionHandler;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\FileSystem;

/**
 * Class implemented by Symfony translation component
 * @package Xinax\LaravelGettext\Translators
 */
class Symfony implements TranslatorInterface
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
     * Symfony translator
     * @var SymfonyTranslator
     */
    protected $symfonyTranslator;

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
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->session = $sessionHandler;
        $this->adapter = $adapter;
        $this->fileSystem = $fileSystem;

        // Symfony component incompatible with php-gettext module
        if (function_exists('gettext')) {
            throw new \Exception(
                "You must disable/uninstall 'php-gettext' in order to use the Symfony handler"
            );
        }

        // General domain
        $this->domain = $this->configuration->getDomain();

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();

        // Sets defaults for boot
        $locale = $this->session->get($this->configuration->getLocale());
        $this->setLocale($locale);

        $this->symfonyTranslator = $this->getTranslator();
    }

    /**
     * Sets the current locale code
     */
    public function setLocale($locale)
    {
        $this->session->set($locale);
        $this->locale = $locale;
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
        return true;
    }

    /**
     * Return the current locale
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->locale;
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
     * @param   String $domain
     * @throws  Exceptions\UndefinedDomainException If domain is not defined
     * @return  self
     */
    public function setDomain($domain)
    {
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

    /**
     * Translates a message using the Symfony translation component
     *
     * @param $message
     * @return string
     */
    public function translate($message)
    {
        return $this->symfonyTranslator->trans($message, [], $this->domain, $this->locale);
    }


    protected function getTranslator()
    {
        if (config('app.debug') && !env('NEVER_FORGET_CACHE', false)) {
            Cache::forget('po_cache');
        }

        return Cache::rememberForever('po_cache', function () {
            $basePath = 'resources/lang/i18n';
            $locales = $this->configuration->getSupportedLocales();

            $translator = new SymfonyTranslator(config('app.locale'));
            $translator->addLoader('po', new PoFileLoader());
            $translator->setFallbackLocales([config('app.locale')]);

            foreach ($locales as $locale) {
                $path = base_path($basePath . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'LC_MESSAGES');
                $file = $path . DIRECTORY_SEPARATOR . 'messages.po';

                if (file_exists($file)) {
                    $translator->addResource('po', $file, $locale);
                    $translator->getCatalogue($locale);
                }
            }
            return $translator;
        });
    }
}