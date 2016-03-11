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
class Symfony extends BaseTranslator implements TranslatorInterface
{
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
     * Translates a message using the Symfony translation component
     *
     * @param $message
     * @return string
     */
    public function translate($message)
    {
        return $this->symfonyTranslator->trans($message, [], $this->domain, $this->locale);
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator()
    {
        if (config('app.debug') && !env('NEVER_FORGET_CACHE', false)) {
            Cache::forget('po_cache');
        }

        return Cache::rememberForever('po_cache', function(){
            return $this->createTranslator();
        });
    }

    /**
     * Creates a new translator instance
     *
     * @return SymfonyTranslator
     */
    protected function createTranslator()
    {
        $locales = $this->configuration->getSupportedLocales();

        $translator = new SymfonyTranslator(config('app.locale'));
        $translator->addLoader('po', new PoFileLoader());
        $translator->setFallbackLocales([
            $this->configuration->getFallbackLocale()
        ]);

        foreach ($locales as $locale) {

            $file = $this->fileSystem->makePOFilePath($locale, $this->domain);

            if (file_exists($file)) {
                $translator->addResource('po', $file, $locale);
                $translator->getCatalogue($locale);
            }
        }

        return $translator;
    }


}