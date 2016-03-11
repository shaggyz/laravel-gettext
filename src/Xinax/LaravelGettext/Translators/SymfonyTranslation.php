<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

class SymfonyTranslation
{
    private static $instance;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = self::createInstance();
        }
        return self::$instance;
    }

    protected static function createInstance()
    {
        if (config('app.debug') && !env('NEVER_FORGET_CACHE', false)) {
            Cache::forget('po_cache');
        }

        return Cache::rememberForever('po_cache', function () {
            $basePath = 'resources/lang/i18n';

            $locales = [
                'de_DE',
                'en_US',
            ];

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