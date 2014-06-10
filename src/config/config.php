<?php

return array(

	/**
	 * Default locale: this will be the default for your application all 
	 * localized strings. Is to be supposed that all strings are written 
	 * on this language.
	 */
	'locale' => 'es_ES',

	/**
	 * Supported locales: An array containing all allowed locales 
	 */
	'supported-locales' => array(
		'es_ES',
		'en_US',
		'it_IT',
		'es_AR',
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
	 * Fallback locale: When default locale is not available
	 */
	'fallback-locale' => 'en_US',

	/**
	 * Domain used for translations: It is the file name for .po and .mo files
	 */
	'domain' => 'messages',

	/**
	 * Base translation directory path (don't use trailing slash)
	 */
	'translations-path' => 'lang',

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
	 * Sync laravel: A flag that determines if the laravel built-in locale must 
	 * be changed when you call LaravelGettext::setLocale.
	 */
	'sync-laravel' => true,

);