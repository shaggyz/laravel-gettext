<?php

// Used for testing
// return array(

return array(

    /**
     * Session identifier: Key under which the current locale will be stored.
     */
    'session-identifier' => 'laravel-gettext-locale',

    /**
     * Default locale: this will be the default for your application.
     * Is to be supposed that all strings are written in this language.
     */
    'locale' => 'en_US',

    /**
     * Supported locales: An array containing all allowed languages
     */
    'supported-locales' => array(
        'en_US',
        'es_AR',
        'de_DE'
    ),

    /**
     * Default charset encoding.
     */
    'encoding' => 'UTF-8',

    /**
     * -----------------------------------------------------------------------
     * All standard configuration ends here. The following values
     * are only for special cases.
     * -----------------------------------------------------------------------
     **/

    /**
     * Locale categories to set
     */
    'categories' => [
        'LC_ALL',
    ],

    /**
     * Base translation directory path relative to base-path
     * (don't use trailing slash)
     */
    'translations-path' => 'lang',

    /**
     * Relative path to the app folder: is used on .po header files
     */
    'relative-path' => '../../../../../app',

    /**
     * Fallback locale: When default locale is not available
     */
    'fallback-locale' => 'es_AR',

    /**
     * Default domain used for translations: It is the file name for .po and .mo files
     */
    'domain' => 'messages',

    /**
     * Project name: is used on .po header files
     */
    'project' => 'MultilanguageLaravelApplication',

    /**
     * Translator contact data (used on .po headers too)
     */
    'translator' => 'James Translator <james@translations.colm>',

    /**
     * Paths where Poedit will search recursively for strings to translate.
     * All paths are relative to "base-path option" (don't use trailing slash).
     *
     * If you have already .po files with translations and the need to add
     * another directory remember to call artisan gettext:update after do this.
     */
    /*'source-paths' => array(
		'controllers',
		'views',
		'storage/views',
	),*/

    /**
     * Multidomain directory paths. If you want separate your translations in
     * different files, just must wrap your paths into a domain name.
     * Paths on top-level will be associated to the default domain file,
     * for example:
     */
    'source-paths' => array(

        // frontend domain
        'frontend' => array(
            'controllers',
            'views/frontend'
        ),

        // backend domain
        'backend' => array(
            'views/backend'
        ),

        // messages domain
        'messages' => [
            'views/messages'
        ],

        // default domain (messages)
        'views/misc',
    ),

    /**
     * Sync laravel: A flag that determines if the laravel built-in locale must
     * be changed when you call LaravelGettext::setLocale.
     */
    'sync-laravel' => true,

    /**
     * Use custom locale that is not supported by the system
     */
    'custom-locale' => false,


    /**
     * The adapter used to sync the laravel built-in locale
     */
    'adapter' => \Xinax\LaravelGettext\Testing\Adapter\TestAdapter::class,

    /**
     * Where to store the current locale/domain
     *
     * By default, in the session.
     * Can be changed for only memory or your own storage mechanism
     *
     * @see \Xinax\LaravelGettext\Storages\Storage
     */
    'storage' => \Xinax\LaravelGettext\Storages\SessionStorage::class,
);
