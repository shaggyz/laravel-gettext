<?php

return array(

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
     * All paths in this configuration are relative to base-path
     * (in laravel is the app directory by default)
     */
    'base-path' => __DIR__ . '../../../../..',

    /**
     * Base translation directory path (don't use trailing slash)
     */
    'translations-path' => 'lang',

	/**
	 * Fallback locale: When default locale is not available
	 */
	'fallback-locale' => 'en_US',

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
	 * Paths where PoEdit will search recursively for strings to translate. 
	 * All paths are relative to app/ (don't use trailing slash).
	 *
	 * If you have already .po files with translations and the need to add 
	 * another directory remember to call artisan gettext:update after do this.
	 */
	'source-paths' => array(
		'controllers',
		'views',
	),

	/**
	 * Multidomain directory paths. If you want separate your translations in 
	 * different files, just must wrap your paths into domain name. 
	 * Paths on top-level will be associated to the default domain file, 
	 * for example:
	 */
	/*'source-paths' => array(
		'frontend' => array(
			'controllers',
			'views/frontend'
		),
		'backend' => array(
			'views/backend'
		),
		'storage/views'
	),*/

	/**
	 * Sync laravel: A flag that determines if the laravel built-in locale must 
	 * be changed when you call LaravelGettext::setLocale.
	 */
	'sync-laravel' => true,

);