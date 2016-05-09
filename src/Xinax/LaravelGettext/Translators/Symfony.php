<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Cache;

use Xinax\LaravelGettext\Config\Models\Config;
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
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     */
    public function __construct(
        Config $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter = $adapter;
        $this->fileSystem = $fileSystem;

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();
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
        return $this->symfonyTranslator->trans($message, [], $this->getDomain(), $this->getLocale());
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator()
    {
        if (config('app.debug')) {
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

        $translator = new SymfonyTranslator($this->getLocale());
        $translator->addLoader('po', new PoFileLoader());
        $translator->setFallbackLocales([
            $this->configuration->getFallbackLocale()
        ]);

        foreach ($locales as $locale) {

            $file = $this->fileSystem->makePOFilePath($locale, $this->getDomain());

            if (file_exists($file)) {
                $translator->addResource('po', $file, $locale, $this->getDomain());
                $translator->getCatalogue($locale);
            }
        }

        return $translator;
    }

    /**
     * Translates a plural string
     *
     * @param $singular
     * @param $plura
     * @param $count
     */
    public function translatePlural($singular, $plural, $count)
    {
        return $this->symfonyTranslator->transChoice($singular . '|' . $plural, $count);
    }

}